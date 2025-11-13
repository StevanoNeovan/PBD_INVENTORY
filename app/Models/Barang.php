<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'idbarang';
    public $timestamps = false;

    protected $fillable = ['jenis', 'nama', 'idsatuan', 'status', 'harga'];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'idsatuan');
    }

    public function detailPengadaan(): HasMany
    {
        return $this->hasMany(DetailPengadaan::class, 'idbarang');
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class, 'idbarang');
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'idbarang');
    }

    public function kartuStok(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'idbarang');
    }
}

