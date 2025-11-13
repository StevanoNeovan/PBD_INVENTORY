<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailRetur extends Model
{
    protected $table = 'detail_retur';
    protected $primaryKey = 'iddetail_retur';
    public $timestamps = false;

    protected $fillable = ['jumlah', 'alasan', 'idretur', 'iddetail_penerimaan'];

    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'idretur');
    }

    public function detailPenerimaan(): BelongsTo
    {
        return $this->belongsTo(DetailPenerimaan::class, 'iddetail_penerimaan');
    }
}

