<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
            )
            ->when($request->role, fn($q) => $q->role($request->role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users->through(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'email'      => $u->email,
                'role'       => $u->role,
                'is_active'  => $u->is_active,
                'created_at' => $u->created_at->format('d M Y'),
                'last_login' => $u->updated_at->diffForHumans(),
            ]),
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role'     => 'required|in:admin,bidan,kader,ortu',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'no_hp'     => $data['no_hp'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => "required|email|unique:users,email,{$user->id}",
            'role'     => 'required|in:admin,bidan,kader,ortu',
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'nullable|min:8',
        ]);

        $user->update([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'no_hp'    => $data['no_hp'] ?? null,
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function toggleActive(User $user)
    {
        // Jangan nonaktifkan diri sendiri
        abort_if($user->id === auth()->id(), 403, 'Tidak bisa menonaktifkan akun sendiri.');

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Pengguna berhasil {$status}.");
    }
}
