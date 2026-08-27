<?php
namespace InnoShop\RestAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use InnoShop\Common\Models\Admin;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof Admin) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
