<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Route;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // prepare variables
        $userTypeID = Auth::user()->user_type_id;
        $routeName = Route::currentRouteName();
        $blackListRoutes = ['stations', 'stationManagers', 'users'];
        $whiteListRoutes = ['admin.stationManagers.index', 'admin.stationManagers.edit', 'admin.stationManagers.update'];

        // check user has permission to access the page
        if ($userTypeID == 2) {
            $include = (str_replace($blackListRoutes, '', $routeName) != $routeName);

            $isProfileChange = (str_replace($whiteListRoutes, '', $routeName) != $routeName);;
            if ($include && $isProfileChange === false) {
                abort(403);
            }
        }

        return $next($request);
    }
}
