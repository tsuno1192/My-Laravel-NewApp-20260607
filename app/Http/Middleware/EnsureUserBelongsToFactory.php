<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserBelongsToFactory
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
    $factoryId = $request->route('factory_id'); // URLパラメータから工場IDを取得

    // もしそのユーザーが指定された工場にアクセスする権限がなければ禁止
    if ($user->factory_id != $factoryId) {
        abort(403, 'この工場へのアクセス権限がありません。');
    }

    return $next($request);

    }
}
