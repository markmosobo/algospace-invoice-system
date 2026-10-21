<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogger
{
    public function record(
        string $event,
        string $description,
        ?Model $auditable = null,
        array $properties = [],
        ?Request $request = null,
        ?int $userId = null
    ): AuditLog {
        $request ??= request();

        $requestId = $request->header('X-Request-ID');

        return AuditLog::create([
            'user_id' => $userId ?? auth()->id(),

            'event' => $event,
            'description' => $description,

            'auditable_type' => $auditable
                ? $auditable->getMorphClass()
                : null,

            'auditable_id' => $auditable?->getKey(),

            'properties' => $properties ?: null,

            'ip_address' => $request->ip(),

            'user_agent' => Str::limit(
                (string) $request->userAgent(),
                2000,
                ''
            ),

            'request_id' => Str::isUuid($requestId)
                ? $requestId
                : null,
        ]);
    }
}