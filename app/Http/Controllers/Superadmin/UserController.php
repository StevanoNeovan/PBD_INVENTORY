<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Menggunakan view v_data_user
            $users = DB::select("SELECT * FROM v_data_user");
            
            return view('superadmin.user.index', compact('users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Ambil data role untuk dropdown
            $roles = DB::select("SELECT * FROM v_data_role");
            
            return view('superadmin.user.create', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:45|unique:user,username',
            'password' => 'required|string|min:6',
            'idrole' => 'required|integer|exists:role,idrole'
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'idrole.required' => 'Role wajib dipilih'
        ]);

        try {
            DB::statement("
                INSERT INTO user (username, password, idrole) 
                VALUES (?, ?, ?)
            ", [
                $request->username,
                Hash::make($request->password),
                $request->idrole
            ]);

            return redirect()->route('superadmin.user.index')
                           ->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = DB::select("
                SELECT * FROM v_data_user WHERE iduser = ?
            ", [$id]);

            if (empty($user)) {
                return redirect()->route('superadmin.user.index')
                               ->with('error', 'User tidak ditemukan');
            }

            return view('superadmin.user.show', ['user' => $user[0]]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = DB::select("SELECT * FROM user WHERE iduser = ?", [$id]);
            
            if (empty($user)) {
                return redirect()->route('superadmin.user.index')
                               ->with('error', 'User tidak ditemukan');
            }

            $roles = DB::select("SELECT * FROM v_data_role");
            
            return view('superadmin.user.edit', [
                'user' => $user[0],
                'roles' => $roles
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'username' => 'required|string|max:45|unique:user,username,' . $id . ',iduser',
            'password' => 'nullable|string|min:6',
            'idrole' => 'required|integer|exists:role,idrole'
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'idrole.required' => 'Role wajib dipilih'
        ]);

        try {
            // Update tanpa password jika tidak diisi
            if ($request->filled('password')) {
                DB::statement("
                    UPDATE user 
                    SET username = ?, password = ?, idrole = ?
                    WHERE iduser = ?
                ", [
                    $request->username,
                    Hash::make($request->password),
                    $request->idrole,
                    $id
                ]);
            } else {
                DB::statement("
                    UPDATE user 
                    SET username = ?, idrole = ?
                    WHERE iduser = ?
                ", [
                    $request->username,
                    $request->idrole,
                    $id
                ]);
            }

            return redirect()->route('superadmin.user.index')
                           ->with('success', 'User berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Cek apakah user yang login mencoba menghapus dirinya sendiri
            if (auth()->user()->iduser == $id) {
                return redirect()->back()
                               ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
            }

            DB::statement("DELETE FROM user WHERE iduser = ?", [$id]);

            return redirect()->route('superadmin.user.index')
                           ->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Filter user by role using stored procedure
     */
    public function filterByRole(Request $request)
    {
        try {
            $idrole = $request->get('idrole');
            
            if ($idrole) {
                // Menggunakan stored procedure sp_get_user_by_role
                $users = DB::select("CALL sp_get_user_by_role(?)", [$idrole]);
            } else {
                $users = DB::select("SELECT * FROM v_data_user");
            }

            $roles = DB::select("SELECT * FROM v_data_role");

            return view('superadmin.user.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal filter data: ' . $e->getMessage());
        }
    }
}