@extends('layouts.master')

@section('title', 'Detail User')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-user-circle"></i> Detail User</h2>
        </div>
        <div>
            <a href="{{ route('superadmin.user.edit', $user->iduser) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('superadmin.user.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi User</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="35%">ID User</th>
                            <td>: <strong>#{{ $user->iduser }}</strong></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>: <strong>{{ $user->username }}</strong></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>: 
                                @if($user->nama_role == 'Super_Admin')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-crown"></i> {{ $user->nama_role }}
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="fas fa-user"></i> {{ $user->nama_role }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection