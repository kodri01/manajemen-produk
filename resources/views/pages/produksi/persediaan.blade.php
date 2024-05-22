@extends('layouts.main')

@section('content')
    <div class="wrapper-table bg-white rounded ">
        <div class="card shadow ">
            <div class="card-header">
                <h4 class="mb-2 font-weight-bold text-primary">{{ $judul }}</h4>

                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if (Session::has('error'))
                    <div class="alert alert-danger">
                        {{ Session::get('error') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body" id="tableStok">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Jumlah Stok</th>

                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Satuan</th>
                            <th>Jumlah Stok</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($products as $produk)
                            @php
                                $id_produk = $produk->id;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $produk->kode_barang }}</td>
                                <td>{{ $produk->nama_barang }}</td>
                                <td>{{ $produk->satuan }}</td>
                                <td>
                                    @php
                                        $totalStokMasuk = $produk->stokMasuk
                                            ->where('produk_id', $id_produk)
                                            ->sum('stok_masuk');
                                        $totalStokKeluar = $produk->stokKeluar
                                            ->where('produk_id', $id_produk)
                                            ->sum('stok_keluar');
                                    @endphp
                                    {{ $totalStokMasuk - $totalStokKeluar }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="stokModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tambah Data Barang</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('add.product') }}" method="post">
                            <div class="modal-body">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating mb-3">
                                            <input type="text"
                                                class="form-control @error('nama_barang') is-invalid @enderror"
                                                id="floatingInput" placeholder="Nama Barang" name="nama_barang">
                                            <label for="floatingInput">Nama Barang</label>
                                        </div>
                                        @error('nama_barang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating">
                                            <select class="form-select form-control @error('satuan') is-invalid @enderror"
                                                id="floatingSelect" aria-label="Floating label select example"
                                                name="satuan">
                                                <option selected disabled>Pilih Satuan Produk</option>
                                                <option value="L">Liter</option>
                                                <option value="KG">KG</option>
                                                <option value="GR">Gram</option>
                                                <option value="PCS">PCS</option>
                                                <option value="UNIT">Unit</option>
                                            </select>
                                            <label for="floatingSelect">Satuan</label>
                                        </div>
                                        @error('satuan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                                id="floatingInput" placeholder="Harga Barang" name="harga">
                                            <label for="floatingInput">@ Harga</label>
                                        </div>
                                        @error('harga')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-warning" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary" type="submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="produkModalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form id="editprodukForm" action="{{ route('update.product') }}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editProdukId">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Data Barang </h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                            class="form-control @error('nama_barang') is-invalid @enderror"
                                            id="editProdukBarang" placeholder="Invoice" name="nama_barang">
                                        <label for="editProdukBarang">Nama Barang</label>
                                    </div>
                                    @error('nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating">
                                        <select class="form-select form-control @error('satuan') is-invalid @enderror"
                                            id="editProdukSatuan" aria-label="Floating label select example"
                                            name="satuan">
                                            <option selected disabled>Pilih Satuan Produk</option>
                                            <option value="L">Liter</option>
                                            <option value="KG">KG</option>
                                            <option value="GR">Gram</option>
                                            <option value="PCS">PCS</option>
                                            <option value="UNIT">Unit</option>
                                        </select>
                                        <label for="editProdukSatuan">Satuan</label>
                                    </div>
                                    @error('satuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('harga') is-invalid @enderror"
                                            id="editProdukHarga" placeholder="Invoice" name="harga">
                                        <label for="editProdukHarga">@ Harga</label>
                                    </div>
                                    @error('harga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-warning" type="button" data-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                $('#produkModalEdit').on('show.bs.modal', function(event) {
                    var button = $(event.relatedTarget); // Button that triggered the modal
                    var produkId = button.data('id'); // Extract info from data-* attributes

                    // AJAX request to get the user data
                    $.ajax({
                        url: '/produks/' + produkId + '/edit',
                        method: 'GET',
                        success: function(data) {
                            $('#editProdukId').val(data.id);
                            $('#editProdukBarang').val(data.nama_barang);
                            $('#editProdukSatuan').val(data.satuan);
                            $('#editProdukHarga').val(data.harga);
                        }
                    });
                });
            });
        </script>
    @endsection
