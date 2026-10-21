<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SystemLogController extends Controller
{
    private const LEVELS = [
        'EMERGENCY',
        'ALERT',
        'CRITICAL',
        'ERROR',
        'WARNING',
        'NOTICE',
        'INFO',
        'DEBUG',
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search'   => ['nullable', 'string', 'max:200'],
            'level'    => ['nullable', 'string', 'in:EMERGENCY,ALERT,CRITICAL,ERROR,WARNING,NOTICE,INFO,DEBUG'],
            'page'     => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 20);
        $search = mb_strtolower(trim($validated['search'] ?? ''));
        $levelFilter = $validated['level'] ?? null;

        $logDirectory = storage_path('logs');

        if (!File::isDirectory($logDirectory)) {
            return response()->json([
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'per_page' => $perPage,
                'total' => 0,
            ]);
        }

        // Only read Laravel log files; never accept a path from the request.
        $files = collect(File::files($logDirectory))
            ->filter(function ($file) {
                return preg_match(
                    '/^laravel.*\.log$/i',
                    $file->getFilename()
                );
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            })
            ->take(14);

        $entries = [];

        foreach ($files as $file) {
            $contents = $this->readFileTail($file->getPathname());

            foreach ($this->parseEntries($contents, $file->getFilename()) as $entry) {
                if ($levelFilter && $entry['level'] !== $levelFilter) {
                    continue;
                }

                if ($search !== '') {
                    $haystack = mb_strtolower(
                        $entry['timestamp'] . ' ' .
                        $entry['level'] . ' ' .
                        $entry['message'] . ' ' .
                        $entry['context'] . ' ' .
                        $entry['details'] . ' ' .
                        $entry['file']
                    );

                    if (!str_contains($haystack, $search)) {
                        continue;
                    }
                }

                $entries[] = $entry;
            }
        }

        // Newest entries first.
        usort($entries, function ($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        $total = count($entries);
        $lastPage = max(1, (int) ceil($total / $perPage));

        // Keep an out-of-range page from returning confusing results.
        $page = min($page, $lastPage);

        return response()->json([
            'data' => array_slice(
                $entries,
                ($page - 1) * $perPage,
                $perPage
            ),
            'current_page' => $page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
        ]);
    }

    private function readFileTail(string $path): string
    {
        // Read only the end of each file to limit memory usage.
        $maxBytes = 1024 * 1024; // 1 MB per file

        $handle = @fopen($path, 'rb');

        if (!$handle) {
            return '';
        }

        try {
            $size = filesize($path);

            if ($size === false || $size === 0) {
                return '';
            }

            $start = max(0, $size - $maxBytes);

            if ($start > 0) {
                fseek($handle, $start);
                // Discard a potentially incomplete first line.
                fgets($handle);
            } else {
                rewind($handle);
            }

            $contents = stream_get_contents($handle);

            return $contents === false ? '' : $contents;
        } finally {
            fclose($handle);
        }
    }

    private function parseEntries(string $contents, string $filename): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $contents);
        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match(
                '/^\[(.*?)\]\s+([a-zA-Z0-9_-]+)\.(EMERGENCY|ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG):\s?(.*)$/',
                $line,
                $matches
            )) {
                if ($current !== null) {
                    $entries[] = $this->finishEntry($current);
                }

                $current = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => strtoupper($matches[3]),
                    'raw' => $matches[4],
                    'file' => $filename,
                ];
            } elseif ($current !== null) {
                // Stack traces and context may span multiple lines.
                $current['raw'] .= "\n" . $line;
            }
        }

        if ($current !== null) {
            $entries[] = $this->finishEntry($current);
        }

        return $entries;
    }

    private function finishEntry(array $entry): array
    {
        $raw = $entry['raw'];
        $message = $raw;
        $context = '';
        $details = '';

        // Laravel commonly appends context as JSON after the message.
        $position = strpos($raw, ' {');

        if ($position !== false) {
            $candidate = trim(substr($raw, $position + 1));
            $decoded = json_decode($candidate, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $message = trim(substr($raw, 0, $position));
                $context = json_encode(
                    $decoded,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                ) ?: $candidate;
            }
        }

        // If the message contains a stack trace, show it separately.
        $tracePosition = strpos($message, "\n");

        if ($tracePosition !== false) {
            $details = trim(substr($message, $tracePosition + 1));
            $message = trim(substr($message, 0, $tracePosition));
        }

        return [
            'timestamp' => $entry['timestamp'],
            'environment' => $entry['environment'],
            'level' => $entry['level'],
            'message' => $message,
            'context' => $context,
            'details' => $details,
            'file' => $entry['file'],
        ];
    }
}