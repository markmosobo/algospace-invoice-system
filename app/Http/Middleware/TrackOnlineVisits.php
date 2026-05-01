<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\OnlineVisit;

class TrackOnlineVisits
{
    public function handle(Request $request, Closure $next)
    {
        // allow request to continue first (important for performance stability)
        $response = $next($request);

        // ignore API noise if needed (optional safety)
        if ($request->is('api/*')) {
            return $response;
        }

        // get or create visitor ID (cookie-based tracking)
        $visitorId = $request->cookie('visitor_id');

        if (!$visitorId) {
            $visitorId = (string) Str::uuid();

            cookie()->queue(
                cookie('visitor_id', $visitorId, 60 * 24 * 365) // 1 year
            );
        }

        // prevent logging infinite assets (optional but recommended)
        if (
            str_contains($request->path(), 'storage') ||
            str_contains($request->path(), 'assets') ||
            str_contains($request->path(), '.js') ||
            str_contains($request->path(), '.css') ||
            str_contains($request->path(), '.map')
        ) {
            return $response;
        }

        // store visit
        OnlineVisit::create([
            'visitor_id' => $visitorId,
            'user_id'    => Auth::id(),
            'ip'         => $request->ip(),
            'url'        => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'method'     => $request->method(),
            'referer'    => $request->headers->get('referer'),
            'visited_at' => now(),
        ]);

        return $response;
    }
}