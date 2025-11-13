<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerimaan extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'idpenerimaan';
    public $timestamps = false;

    protected $fillable = ['created_at', 'status', 'idpengadaan', 'iduser'];

    public function pengadaan(): BelongsTo
    {
        return $this->belongsTo(Pengadaan::class, 'idpengadaan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class, 'idpenerimaan');
    }

    public function retur(): HasMany
    {
        return $this->hasMany(Retur::class, 'idpenerimaan');
    }
}

