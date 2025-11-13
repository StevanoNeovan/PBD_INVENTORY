@extends('layouts.master')

@section('title', 'Master Role')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-user-shield"></i> Master Role</h2>
            <p class="text-muted mb-0">Daftar role sistem</p>
        </div>
    </div>

    <!-- Card Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Role</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tableRole">
                    <thead class="table-light">
                        <tr>
                            <th width="80" class="text-center">ID</th>
                            <th>Nama Role</th>
                            <th width="150" class="text-center">Total User</th>
                            <th width="200" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td class="text-center">{{ $role->idrole }}</td>
                            <td>
                                <strong>{{ $role->nama_role }}</strong>
                                @if($role->nama_role == 'Super_Admin')
                                    <span class="badge bg-danger ms-2">Full Access</span>
                                @else
                                    <span class="badge bg-info ms-2">Limited Access</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6">{{ $role->total_users }} User</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('superadmin.user.index', ['role' => $role->idrole]) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-users"></i> Lihat User
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada data role
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3 border-left-info shadow">
        <div class="card-body">
            <h6 class="text-info"><i class="fas fa-info-circle"></i> Informasi</h6>
            <p class="mb-0 small">
                Role sistem bersifat tetap dan tidak dapat diubah. Role menentukan hak akses pengguna dalam sistem.
            </p>
            <ul class="small mb-0 mt-2">
                <li><strong>Super_Admin:</strong> Akses penuh ke semua fitur sistem</li>
                <li><strong>Admin:</strong> Akses terbatas untuk transaksi harian</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
</style>
@endpush