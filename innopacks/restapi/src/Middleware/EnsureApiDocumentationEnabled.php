<?php
namespace InnoShop\RestAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates Scribe HTML, OpenAPI, and Postman routes behind system setting {@see api_docs_enabled()}.
 */
class EnsureApiDocumentationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! api_docs_enabled()) {
            abort(404);
        }

        return $next($request);
    }
}
