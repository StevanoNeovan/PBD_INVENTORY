<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempDetailPenjualan extends Model
{
    protected $table = 'temp_detail_penjualan';
    protected $primaryKey = 'idtemp';
    public $timestamps = false;

    protected $fillable = [
        'idbarang', 'jumlah', 'harga_beli', 'idmargin_penjualan',
        'harga_jual', 'subtotal', 'created_at'
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'idbarang');
    }

    public function marginPenjualan(): BelongsTo
    {
        return $this->belongsTo(MarginPenjualan::class, 'idmargin_penjualan');
    }
}
