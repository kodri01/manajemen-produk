<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UsersController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('tologin');

Route::group(['middleware' => 'auth'], function () {
    //Log Out
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    //profile
    Route::get('profile/{id}', [ProfileController::class, 'index'])->name('profile');
    Route::put('photo/update/{id}', [ProfileController::class, 'update_profile'])->name('update.photo');
    Route::put('profile/update/{id}', [ProfileController::class, 'update'])->name('update.profile');

    //dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //invoice
    Route::get('invoice/orderan/{no}', [InvoiceController::class, 'invoice_orderan'])->name('invoice.order');
    Route::get('invoice/resep/{no}', [InvoiceController::class, 'invoice_resep'])->name('invoice.resep');

    Route::group(['middleware' => 'role:administrator'], function () {
        //users
        Route::get('users', [UsersController::class, 'index'])->name('users');
        Route::post('/users_add', [UsersController::class, 'store'])->name('add.users');
        Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('edit.user');
        Route::put('/users', [UsersController::class, 'update'])->name('update.users');
        Route::delete('/users/delete/{id}', [UsersController::class, 'destroy'])->name('delete.users');

        //supplier
        Route::get('supplier', [SupplierController::class, 'index'])->name('supplier');
        Route::post('supplier', [SupplierController::class, 'store'])->name('add.supplier');
        Route::get('/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('edit.supplier');
        Route::put('/supplier', [SupplierController::class, 'update'])->name('update.supplier');
        Route::delete('/supplier/delete/{id}', [SupplierController::class, 'destroy'])->name('delete.supplier');

        //Transaksi
        Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::delete('/transaksi/delete/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.delete');
        Route::get('invoice/transaksi/{no}', [InvoiceController::class, 'invoice_transaksi'])->name('invoice.transaksi');
    });

    Route::group(['middleware' => ['role:administrator|gudang']], function () {
        //Stok Produk
        Route::get('produks', [ProductController::class, 'index'])->name('product');
        Route::post('produks', [ProductController::class, 'store'])->name('add.product');
        Route::get('/produks/{id}/edit', [ProductController::class, 'edit'])->name('edit.product');
        Route::put('/produks', [ProductController::class, 'update'])->name('update.product');
        Route::delete('/produks/delete/{id}', [ProductController::class, 'destroy'])->name('delete.product');

        //Produk Masuk
        Route::get('masuk', [ProductController::class, 'masuk'])->name('product.masuk');
        Route::post('/masuk_add', [ProductController::class, 'masuk_store'])->name('add.masuk');
        Route::get('/masuk/{id}/edit', [ProductController::class, 'edit_masuk'])->name('edit.masuk');
        Route::put('/masuk', [ProductController::class, 'update_masuk'])->name('update.masuk');
        Route::delete('/masuk/delete/{id}', [ProductController::class, 'destroy_masuk'])->name('delete.masuk');

        //Produk Keluar
        Route::get('keluar', [ProductController::class, 'keluar'])->name('product.keluar');
        Route::post('/keluar_add', [ProductController::class, 'keluar_store'])->name('add.keluar');
        Route::get('/keluar/{id}/edit', [ProductController::class, 'edit_keluar'])->name('edit.keluar');
        Route::put('/keluar', [ProductController::class, 'update_keluar'])->name('update.keluar');
        Route::delete('/keluar/delete/{id}', [ProductController::class, 'destroy_keluar'])->name('delete.keluar');

        //Order Produk
        Route::get('order', [OrderController::class, 'index'])->name('product.order');
        Route::post('/order', [OrderController::class, 'store'])->name('order.store');
        Route::get('/order/details/{no}', [OrderController::class, 'show'])->name('order.details');
        Route::delete('/order/delete/{id}', [OrderController::class, 'destroy'])->name('order.delete');
    });

    Route::group(['middleware' => ['role:administrator|produksi']], function () {
        //Resep
        Route::get('resep', [ProduksiController::class, 'index'])->name('resep');
        Route::post('/resep/add', [ProduksiController::class, 'store'])->name('add.resep');
        Route::get('/resep/bahan', [ProduksiController::class, 'resep_bahan'])->name('resep.bahan');
        Route::get('/resep/details/{no}', [ProduksiController::class, 'show'])->name('resep.details');
        Route::delete('/resep/delete/{id}', [ProduksiController::class, 'destroy'])->name('resep.delete');

        //Persediaan
        Route::get('persediaan', [ProduksiController::class, 'persediaan'])->name('persediaan');
    });
});