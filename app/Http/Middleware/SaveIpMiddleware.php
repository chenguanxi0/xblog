<?php

namespace App\Http\Middleware;

use App\Http\Repositories\IpRepository;
use Closure;

class SaveIpMiddleware
{
    protected $ipRepository;

    /**
     * SaveIpMiddleware constructor.
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
        $this->ipRepository->createIfNotExisted($request);
        return $next($request);
    }
}
