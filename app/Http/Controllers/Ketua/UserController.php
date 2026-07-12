<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('ketua.user.index');
    }

    public function data(Request $request)
    {
        $query = User::with('anggota');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return datatables()->of($query)
            ->editColumn('is_active', fn($row) => $row->is_active
                ? '<span class="badge bg-success">Aktif</span>'
                : '<span class="badge bg-danger">Nonaktif</span>')
            ->editColumn('role', fn($row) => '<span class="badge bg-primary text-capitalize">' . ucfirst($row->role) . '</span>')
            ->addColumn('action', function ($row) {
                $buttons = '<button onclick="showDetail(' . $row->id . ')" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button> ';
                if ($row->is_active) {
                    $buttons .= '<button onclick="toggleActive(' . $row->id . ', 0)" class="btn btn-sm btn-warning"><i class="fas fa-ban"></i></button> ';
                } else {
                    $buttons .= '<button onclick="toggleActive(' . $row->id . ', 1)" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button> ';
                }
                $buttons .= '<button onclick="showResetPassword(' . $row->id . ')" class="btn btn-sm btn-secondary"><i class="fas fa-key"></i></button>';
                return $buttons;
            })
            ->rawColumns(['is_active', 'role', 'action'])
            ->make(true);
    }

    public function show(User $user)
    {
        $user->load('anggota');
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return response()->json([
            'success' => true,
            'message' => 'User berhasil ' . ($user->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.',
        ]);
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset.',
        ]);
    }
}
