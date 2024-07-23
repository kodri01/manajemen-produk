<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'baku_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'harga',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stokMasuk()
    {
        return $this->hasMany(StokMasuk::class, 'baku_id');
    }

    public function stokKeluar()
    {
        return $this->hasMany(StokKeluar::class, 'baku_id');
    }

    public function order()
    {
        return $this->hasMany(OrderStok::class, 'produk_id');
    }

    public function resep()
    {
        return $this->hasMany(Resep::class, 'produk_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'produk_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            // Hapus data terkait
            $product->stokMasuk()->delete();
            $product->stokKeluar()->delete();
            $product->order()->delete();
        });
    }
}
