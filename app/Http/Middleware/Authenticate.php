<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson() && !$request->ajax()) {
            return route('login');
        } else {
            if ($request->isMethod('post')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir, silahkan muat ulang (tekan tombol F5) untuk memulai lagi.',
                ]);
            } else {
                return route('expired');
            }
        }
    }
}
