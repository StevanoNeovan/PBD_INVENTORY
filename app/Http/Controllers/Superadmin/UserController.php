<?php


namespace App\Http\Controllers\Superadmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('superadmin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('superadmin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:45|unique:user',
            'password' => 'required|string|min:6',
            'idrole' => 'required|exists:role,idrole',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'idrole' => $request->idrole,
        ]);

        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('superadmin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:45|unique:user,username,' . $user->iduser . ',iduser',
            'idrole' => 'required|exists:role,idrole',
        ]);

        $data = $request->only('username', 'idrole');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil dihapus.');
    }
}
