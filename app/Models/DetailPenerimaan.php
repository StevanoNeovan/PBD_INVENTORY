<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'iddetail_penerimaan';
    public $timestamps = false;

    protected $fillable = ['idpenerimaan', 'idbarang', 'jumlah_terima', 'harga_satuan_terima', 'sub_total_terima'];

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class, 'idpenerimaan');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'idbarang');
    }
}
