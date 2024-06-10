<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Models\ServiceAccess;
use Closure;
use Illuminate\Http\Request;

class ServiceAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('ServiceKey');

        if ($header) {
            $token = explode(" ", $header);
            $validate = ServiceAccess::find($token);

            if (!$validate)
                return response()->json(['status' => 400, 'message' => "You can't access this service"]);
            else
                return $next($request);
        } else {
            return response()->json(['status' => 400, 'message' => "You can't access this service"]);
        }
    }
}
