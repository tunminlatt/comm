<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Responder;
use App\Repositories\VolunteerRepository;

class APIToken
{
    public function __construct(
        Responder $responder,
        VolunteerRepository $volunteerRepository
    ) {
        $this->responder = $responder;
        $this->volunteerRepository = $volunteerRepository;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->header('api-token')) {
            $volunteer = $this->volunteerRepository->show('api_token', $request->header('api-token'), [], false);
            if($volunteer->count() > 0){
                return $next($request);
            }
        }
        return $this->responder->customResponse(400, 'Invalid request token!');
    }
}
