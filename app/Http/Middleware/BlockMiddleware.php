<?php

namespace App\Http\Middleware;

use App\Http\Repositories\IpRepository;
use Closure;

class BlockMiddleware
{
    protected $ipRepository;

    /**
     * BlockMiddleware constructor.
     * @param IpRepository $ipRepository
     */
    public function __construct(IpRepository $ipRepository)
    {
        $this->ipRepository = $ipRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!isAdminById(auth()->id()) && $this->ipRepository->isBlocked($request->ip())) {
            return response('Sorry, you are blocked, -_-');
        }
        return $next($request);
    }
}
