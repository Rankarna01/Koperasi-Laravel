@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('breadcrumb')
    <a href="{{ route('ketua.dashboard') }}" class="hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-slate-700">Manajemen User</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Manajemen User</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola akun pengguna sistem</p>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-slate-100">
        <div class="flex flex-wrap gap-2">
            <button onclick="filterRole('')" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-primary-50 text-primary-600">Semua</button>
            <button onclick="filterRole('anggota')" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-600">Anggota</button>
            <button onclick="filterRole('bendahara')" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-600">Bendahara</button>
            <button onclick="filterRole('admin')" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-purple-50 text-purple-600">Admin</button>
            <button onclick="filterRole('ketua')" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-orange-50 text-orange-600">Ketua</button>
        </div>
    </div>
    <div class="p-4">
        <table id="tableUser" class="w-full text-sm">
            <thead>
                <tr>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Nama</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Email</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Role</th>
                    <th class="text-left py-3 px-4 font-semibold text-slate-600">Status</th>
                    <th class="text-center py-3 px-4 font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="font-bold text-slate-800">Detail User</h3>
            <button onclick="closeModal('detailModal')" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <div id="detailContent" class="p-4 space-y-3"></div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="font-bold text-slate-800">Reset Password</h3>
            <button onclick="closeModal('resetModal')" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="resetForm" class="p-4">
            <input type="hidden" id="reset_id">
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Password Baru *</label>
                <input type="password" id="reset_password" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20" required minlength="6">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Konfirmasi Password *</label>
                <input type="password" id="reset_password_confirmation" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20" required minlength="6">
            </div>
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl transition text-sm">
                <i class="fas fa-key mr-1"></i> Reset Password
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let table;
let currentRole = '';

$(document).ready(function() {
    table = $('#tableUser').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("ketua.user.data") }}',
            data: function(d) {
                d.role = currentRole;
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role', orderable: false },
            { data: 'is_active', name: 'is_active', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[0, 'asc']],
    });
});

function filterRole(role) {
    currentRole = role;
    table.ajax.reload();
}

function showDetail(id) {
    $.get('/ketua/user/' + id, function(res) {
        if (res.success) {
            const u = res.data;
            let html = `
                <div class="flex justify-between"><span class="text-xs text-slate-500">Nama</span><span class="text-sm font-medium">${u.name}</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-500">Email</span><span class="text-sm">${u.email}</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-500">Role</span><span class="text-sm capitalize">${u.role}</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-500">Status</span><span class="text-sm">${u.is_active ? 'Aktif' : 'Nonaktif'}</span></div>
                ${u.anggota ? `<div class="flex justify-between"><span class="text-xs text-slate-500">No Anggota</span><span class="text-sm">${u.anggota.no_anggota || '-'}</span></div>` : ''}
            `;
            $('#detailContent').html(html);
            document.getElementById('detailModal').classList.remove('hidden');
        }
    });
}

function toggleActive(id, active) {
    const action = active ? 'mengaktifkan' : 'menonaktifkan';
    if (confirm('Yakin ingin ' + action + ' user ini?')) {
        $.ajax({
            url: '/ketua/user/' + id + '/toggle-active',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(res) {
                if (res.success) {
                    showToast('success', res.message);
                    table.ajax.reload();
                }
            }
        });
    }
}

function showResetPassword(id) {
    document.getElementById('reset_id').value = id;
    document.getElementById('resetModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

$('#resetForm').submit(function(e) {
    e.preventDefault();
    const id = document.getElementById('reset_id').value;
    const password = document.getElementById('reset_password').value;
    const confirmation = document.getElementById('reset_password_confirmation').value;

    if (password !== confirmation) {
        showToast('error', 'Konfirmasi password tidak cocok');
        return;
    }

    $.ajax({
        url: '/ketua/user/' + id + '/reset-password',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            password: password,
            password_confirmation: confirmation,
        },
        success: function(res) {
            if (res.success) {
                showToast('success', res.message);
                closeModal('resetModal');
            }
        }
    });
});
</script>
@endpush
