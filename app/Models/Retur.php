<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retur extends Model
{
    protected $table = 'retur';
    protected $primaryKey = 'idretur';
    public $timestamps = false;

    protected $fillable = ['created_at', 'idpenerimaan', 'iduser'];

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class, 'idpenerimaan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    public function detailRetur(): HasMany
    {
        return $this->hasMany(DetailRetur::class, 'idretur');
    }
}

