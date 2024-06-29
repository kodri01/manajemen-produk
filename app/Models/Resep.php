<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resep extends Model
{
    use HasFactory;

    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'produk_id',
        'no_resep',
        'qty',
        'nama_resep',
        'keterangan',
        'instruksi',
    ];

    public function produk()
    {
        return $this->belongsTo(Product::class);
    }

    public function produkSell()
    {
        return $this->hasMany(ProductSell::class, 'no_resep');
    }
}