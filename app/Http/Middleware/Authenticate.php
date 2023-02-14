<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

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
        if ($request->is('admin/*')) {
            return route('admin.login');
        }
        if ($request->is('student/*')) {
            return route('student.login');
        }
        if ($request->is('university/*')) {
            return route('university.login');
        }
        if ($request->is('employer/*')) {
            return route('employer.login');
        }
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
