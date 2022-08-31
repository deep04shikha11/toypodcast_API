<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\UserRole;
use Illuminate\Http\Request;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){            
            $admin_user = UserRole::where('user_id',Auth::user()->id)->first();
            if ($admin_user->role_id==1) {
                return $next($request);
            }
            abort(response()->json('Only Admin user are allowed', 403));
        }
        else {
            abort(response()->json('user not login', 403));
        }
        
        
    }
}
