<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualan';
    protected $primaryKey = 'iddetail_penjualan';
    public $timestamps = false;

    protected $fillable = ['harga_satuan', 'jumlah', 'subtotal', 'idpenjualan', 'idbarang'];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'idpenjualan');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'idbarang');
    }
}

