<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * 권한 확인을 위한 미들웨어
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @param  string|null  $scopeType
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $permission, $scopeType = null): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $scope = null;

        // $scopeType이 제공된 경우 현재 활성 범위 가져오기
        if ($scopeType === 'organization') {
            $scope = session('current_organization');
        } elseif ($scopeType === 'team') {
            $scope = session('current_team');
        }

        if (!$user->hasPermission($permission, $scope)) {
            abort(403, '이 작업을 수행할 권한이 없습니다.');
        }

        return $next($request);
    }
}
