<?php
namespace InnoShop\Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use InnoShop\Common\Services\VisitTrackingService;

class VisitTrackingMiddleware
{
    /**
     * Visit tracking service instance
     *
     * @var VisitTrackingService
     */
    private VisitTrackingService $visitTrackingService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visitTrackingService = new VisitTrackingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Get session ID
        $sessionId = $request->session()->getId();

        // Get customer ID if authenticated
        $customerId = current_customer()?->id;

        // Track visit
        $this->visitTrackingService->trackVisit($request, $sessionId, $customerId);

        // Process request
        return $next($request);
    }
}
