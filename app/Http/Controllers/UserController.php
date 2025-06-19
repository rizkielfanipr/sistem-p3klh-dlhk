<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return redirect()->route('users.admin');
    }

    public function indexAdmin()
    {
        $users = User::where('role_id', 1)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Admin',
            'buttonText' => 'Admin',
        ]);
    }

    public function indexFrontOffice()
    {
        $users = User::where('role_id', 2)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Front Office',
            'buttonText' => 'Front Office',
        ]);
    }

    public function indexPengguna()
    {
        $users = User::where('role_id', 3)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Pengguna',
            'buttonText' => 'Pengguna',
        ]);
    }

    public function indexPenelaah()
    {
        $users = User::where('role_id', 4)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Penelaah',
            'buttonText' => 'Penelaah',
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_users', 'public');
        }

        User::create([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'foto' => $fotoPath,
        ]);

        switch ($request->role_id) {
            case 1:
                return redirect()->route('users.admin')->with('success', 'Admin berhasil ditambahkan.');
            case 2:
                return redirect()->route('users.fo')->with('success', 'Front Office berhasil ditambahkan.');
            case 3:
                return redirect()->route('users.pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
            case 4:
                return redirect()->route('users.penelaah')->with('success', 'Penelaah berhasil ditambahkan.');
            default:
                return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('foto_users', 'public');
        }

        $user->update([
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'role_id' => $request->role_id,
            'foto' => $user->foto,
        ]);

        switch ($user->role_id) {
            case 1:
                return redirect()->route('users.admin')->with('success', 'Admin berhasil diperbarui.');
            case 2:
                return redirect()->route('users.fo')->with('success', 'Front Office berhasil diperbarui.');
            case 3:
                return redirect()->route('users.pengguna')->with('success', 'Pengguna berhasil diperbarui.');
            case 4:
                return redirect()->route('users.penelaah')->with('success', 'Penelaah berhasil diperbarui.');
            default:
                return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
        }
    }

    public function destroy(User $user)
    {
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $role_id = $user->role_id;
        $user->delete();

        switch ($role_id) {
            case 1:
                return redirect()->route('users.admin')->with('success', 'Admin berhasil dihapus.');
            case 2:
                return redirect()->route('users.fo')->with('success', 'Front Office berhasil dihapus.');
            case 3:
                return redirect()->route('users.pengguna')->with('success', 'Pengguna berhasil dihapus.');
            case 4:
                return redirect()->route('users.penelaah')->with('success', 'Penelaah berhasil dihapus.');
            default:
                return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
        }
    }
}
