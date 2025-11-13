<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $table = 'user';
    protected $primaryKey = 'iduser';
    public $timestamps = false;

    protected $fillable = ['username', 'password', 'idrole'];
    protected $hidden = ['password'];

    // app/Models/User.php
    public function role()
    {
        return $this->belongsTo(Role::class, 'idrole', 'idrole');
    }

    public function pengadaan(): HasMany
    {
        return $this->hasMany(Pengadaan::class, 'iduser');
    }

    public function penerimaan(): HasMany
    {
        return $this->hasMany(Penerimaan::class, 'iduser');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(Retur::class, 'iduser');
    }

    public function marginPenjualan(): HasMany
    {
        return $this->hasMany(MarginPenjualan::class, 'iduser');
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'iduser');
    }
}
