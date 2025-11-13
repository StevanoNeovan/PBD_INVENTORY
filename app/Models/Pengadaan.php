<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengadaan extends Model
{
    protected $table = 'pengadaan';
    protected $primaryKey = 'idpengadaan';
    public $timestamps = false;

    protected $fillable = ['timestamp', 'iduser', 'idvendor', 'subtotal_nilai', 'ppn', 'total_nilai'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'idvendor');
    }

    public function detailPengadaan(): HasMany
    {
        return $this->hasMany(DetailPengadaan::class, 'idpengadaan');
    }

    public function penerimaan(): HasMany
    {
        return $this->hasMany(Penerimaan::class, 'idpengadaan');
    }
}

