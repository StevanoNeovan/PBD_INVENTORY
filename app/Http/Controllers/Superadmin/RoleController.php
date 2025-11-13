<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{

    public function index()
    {
        // Gunakan VIEW dari database
        $roles = DB::table('v_data_role')->get();
        
        // Hitung jumlah user per role
        foreach ($roles as $role) {
            $role->total_users = DB::table('user')
                ->where('idrole', $role->idrole)
                ->count();
        }
        
        return view('superadmin.role.index', compact('roles'));
    }
}