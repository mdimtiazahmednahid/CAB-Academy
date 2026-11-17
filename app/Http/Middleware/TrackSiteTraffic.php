<?php

namespace App\Http\Middleware;

use App\Support\SiteTraffic;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteTraffic
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        SiteTraffic::recordVisit($request, $response);

        return $response;
    }
}
