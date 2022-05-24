<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Traits\ShopifyTrait;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Odin extends BaseMiddleware
{

    use ShopifyTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->verifyWebHook($request) ||
            $this->verifyRequest($request) ||
            $this->verifyRequest($request, true) ||
            $request->has(Helper::hadesHeader()) ||
            auth()->user())
        {
            // ----- set shop if JWT was found ...
            if (auth()->user()) {
                $user = auth()->user();
                $request->request->set('shop', $user->shop->domain);
            }

            // ----- set shop if request was from shopify
            if ($request->hasHeader('X-Shopify-Shop-Domain')) {
                $request->request->set('shop', $request->header('X-Shopify-Shop-Domain'));
            }

            return $next($request);
        } else {
            return response(view('app'));
        }
    }

}
