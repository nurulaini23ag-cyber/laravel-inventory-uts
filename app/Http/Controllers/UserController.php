<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna & pelanggan sistem
     */
    public function index(Request $request)
    {
        $query = User::withCount(['sales', 'purchases']);

        if ($request->filled('role')) {
            if ($request->role === 'customer' || $request->role === 'user') {
                $query->whereIn('role', ['user', 'customer']);
            } else {
                $query->where('role', $request->role);
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        $totalCustomers = User::whereIn('role', ['user', 'customer'])->count();
        $totalStaff     = User::where('role', 'staff')->count();
        $totalAdmin     = User::where('role', 'admin')->count();

        return view('users.index', compact('users', 'totalCustomers', 'totalStaff', 'totalAdmin'));
    }

    /**
     * Simpan pengguna baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', Rule::in(['admin', 'staff', 'user', 'customer'])],
        ]);

        User::create([
            'name'     => $validated['name'],
            'username' => strtolower($validated['username']),
            'email'    => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Akun ' . $validated['name'] . ' (' . strtoupper($validated['role']) . ') berhasil didaftarkan.');
    }

    /**
     * Update role atau data pengguna
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role'     => ['required', Rule::in(['admin', 'staff', 'user', 'customer'])],
        ]);

        $data = [
            'name'     => $validated['name'],
            'username' => strtolower($validated['username']),
            'email'    => strtolower($validated['email']),
            'role'     => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Data akun ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus pengguna
     */
    public function destroy(User $user)
    {
        // Cegah Admin menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cegah penghapusan jika user memiliki transaksi aktif
        if ($user->sales()->count() > 0 || $user->purchases()->count() > 0) {
            return back()->with('error', 'Pengguna ' . $user->name . ' tidak dapat dihapus karena memiliki riwayat transaksi aktif di sistem.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Akun ' . $userName . ' berhasil dihapus.');
    }
}
