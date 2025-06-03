<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginForm(){
        // DB::table('admin')->where('email', 'admin@gmail.com')->update([
        //     'password' => Hash::make('admin123')
        // ]);
        return View('adminLogin');
    }

public function handle($request, Closure $next){
        if(session()->has('loggedUser')){
            return redirect('/login')->with('error', 'Please logoin first');
        }

        return $next($request);
    }

    public function login(Request $request){
        $validate = $request->validate(
            [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]
            );

           
                $user = DB::table('admin')->where('email', $request->email)->first();
                if($user && Hash::check($request->password, $user->password)){
                    Auth::loginUsingId($user->id);
                    session(['loggedUser' => $user]);
                    return redirect()->route('employees.index'); 
                }
                return back()->withErrors(['email' =>'Invalid Credentials']);
    }
    
    
}
