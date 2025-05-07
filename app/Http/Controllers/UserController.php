<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Role Model
     *
     * @var Role
     */
    private $role;

    /**
     * UserController constructor.
     *
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    /**
     * Display a listing of admin users.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAdmin()
    {
        $users = User::where('role_id', 1)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Admin',
            'buttonText' => 'Admin',
        ]);
    }

    /**
     * Display a listing of front office users.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexFrontOffice()
    {
        $users = User::where('role_id', 2)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Front Office',
            'buttonText' => 'Front Office',
        ]);
    }

    /**
     * Display a listing of regular users.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPengguna()
    {
        $users = User::where('role_id', 3)->get();
        return view('users.index', [
            'users' => $users,
            'title' => 'Daftar Pengguna',
            'buttonText' => 'Pengguna',
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->role->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'no_telp'   => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            try {
                $user = User::create([
                    'nama'      => $request->nama,
                    'email'     => $request->email,
                    'password' => Hash::make($request->password),
                    'role_id'   => $request->role_id,
                    'no_telp'   => $request->no_telp,
                ]);
                DB::commit();

                // Redirect ke route yang sesuai berdasarkan role_id
                switch ($request->role_id) {
                    case 1:
                        return redirect()->route('users.admin')->with('success', 'Admin berhasil ditambahkan.');
                    case 2:
                        return redirect()->route('users.fo')->with('success', 'Front Office berhasil ditambahkan.');
                    case 3:
                        return redirect()->route('users.pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
                    default:
                        // Jika role_id tidak valid, redirect ke halaman default atau tampilkan pesan error
                        return redirect()->route('home')->with('error', 'Role pengguna tidak valid.'); // Atau ke route lain yang sesuai
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating user: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan ke database: ' . $e->getMessage())->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Database transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada transaksi database: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = $this->role->all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'no_telp'   => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();
            try {
                $user->nama      = $request->nama;
                $user->email     = $request->email;
                $user->role_id = $request->role_id;
                $user->no_telp = $request->no_telp;

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                $user->save();
                DB::commit();

                // Redirect berdasarkan role_id yang diupdate
                switch ($user->role_id) {
                    case 1:
                        return redirect()->route('users.admin')->with('success', 'Admin berhasil diperbarui.');
                    case 2:
                        return redirect()->route('users.fo')->with('success', 'Front Office berhasil diperbarui.');
                    case 3:
                        return redirect()->route('users.pengguna')->with('success', 'Pengguna berhasil diperbarui.');
                    default:
                        return redirect()->route('home')->with('error', 'Role pengguna tidak valid.');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating user: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui pengguna: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Database transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada transaksi database: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $roleId = $user->role_id; // Simpan role_id sebelum dihapus

        try {
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                // Redirect berdasarkan role_id yang dihapus
                switch ($roleId) {
                    case 1:
                        return redirect()->route('users.admin')->with('success', 'Admin berhasil dihapus.');
                    case 2:
                        return redirect()->route('users.fo')->with('success', 'Front Office berhasil dihapus.');
                    case 3:
                        return redirect()->route('users.pengguna')->with('success', 'Pengguna berhasil dihapus.');
                    default:
                        return redirect()->route('home')->with('error', 'Role pengguna tidak valid.');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error deleting user: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus pengguna: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Database transaction error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan pada transaksi database: ' . $e->getMessage());
        }
    }
}