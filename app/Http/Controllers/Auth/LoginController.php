<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    use AuthenticatesUsers;    protected function authenticated(Request $request, $user)
    {
        if ($user->role->nama_role === 'Super_Admin') {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->role->nama_role === 'Admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect('/home');
    }

      public function username()
    {
        return 'username';
    }
}

  

