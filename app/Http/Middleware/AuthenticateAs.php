<?php
/**
 * @deprecated since 2020-01-26. replaced by policies
 * Used to make sure a user is authenticated with a specific role.
 * This replaces the "auth" middleware.
 * Can be used like 
 * `middleware('authenticate_as:kunde')`
 * See https://laravel.com/docs/6.x/middleware#middleware-parameters
 * 
 * @param role Can be "kunde" or "anbieter"
 * @author martin
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateAs
{
    
    
    /**
     * Redirect path to be set here!
     * 
     * @return Response
     */
    protected function redirect() {
        return redirect('/');
    }
    
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // redirect if not authenticated at all
        if (!Auth::check()) {
            return $this->redirect();
        }
        
        $user = Auth::user();
        
        switch ($role) {
            case 'guest':
                if ($user->role != "guest") {
                    return $this->redirect();
                }
                break;
                
            case 'werkzeugag':
                if ($user->role != 'werkzeugag') {
                    return $this->redirect();
                }
                break;
                
            default:
                throw new \Exception('Invalid parameter given: '. $role);
        }
        
        // everything okay, proceed
        return $next($request);
    }
}
