<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserCrudController extends Controller
{
    public function index(Request $request): View
    {
        return view('crud.admin-users.index', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'users' => AdminUser::query()->orderBy('nama')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        return view('crud.admin-users.form', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'adminUser' => new AdminUser(),
            'schools' => School::query()->orderBy('nama')->get(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:user,nama'],
            'level' => ['required', 'in:admin,guru,user'],
            'id' => ['required', 'integer', 'exists:sekolah,id'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        $data['pass'] = Hash::make($data['password']);
        unset($data['password']);

        AdminUser::query()->create($data);

        return redirect()->route('admin-users.index')->with('status', 'Pengguna admin berhasil ditambahkan.');
    }

    public function edit(Request $request, AdminUser $admin_user): View
    {
        return view('crud.admin-users.form', [
            'sessionUser' => $request->session()->get('legacy_user'),
            'adminUser' => $admin_user,
            'schools' => School::query()->orderBy('nama')->get(),
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, AdminUser $admin_user): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:user,nama,' . $admin_user->idu . ',idu'],
            'level' => ['required', 'in:admin,guru,user'],
            'id' => ['required', 'integer', 'exists:sekolah,id'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        if (! empty($data['password'])) {
            $data['pass'] = Hash::make($data['password']);
        }
        unset($data['password']);

        $admin_user->update($data);

        return redirect()->route('admin-users.index')->with('status', 'Pengguna admin berhasil diperbarui.');
    }

    public function destroy(AdminUser $admin_user): RedirectResponse
    {
        $admin_user->delete();

        return redirect()->route('admin-users.index')->with('status', 'Pengguna admin berhasil dihapus.');
    }
}
