@extends('layouts.main')

@section('content')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $judul }}</h1>
    </div>

    <div class="wrapper-table bg-white rounded ">
        <div class="card shadow ">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold"><a href="#" data-toggle="modal" data-target="#resepModal"
                        class="btn btn-primary ">+ Data Resep</a></h6>
            </div>
        </div>
        <div class="card-body" id="tableStok">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No Resep</th>
                            <th>Nama Resep</th>
                            <th>Deskripsi</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>No Resep</th>
                            <th>Nama Resep</th>
                            <th>Deskripsi</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach ($reseps as $resep)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $resep->no_resep }}</td>
                                <td>{{ $resep->nama_resep }}</td>
                                <td>{{ $resep->keterangan }}</td>
                                <td><a href="{{ route('resep.details', $resep->no_resep) }}"
                                        class="btn btn-primary btn-circle btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('resep.delete', $resep->no_resep) }}" method="post"
                                        class="d-inline">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button class="btn btn-sm btn-circle btn-danger"
                                            onclick="return confirm('Anda Yakin Akan Menghapus Data Ini ?')" type="submit">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="resepModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Data Resep</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="col-sm-6">
                                <form id="formResep" action="{{ route('add.resep') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    {{-- <div class="col-md-12 "> --}}
                                    <div class="row mt-2 mb-4">
                                        <div class="col-sm-6">
                                            <label for="inputState" class="form-label font-weight-bold text-primary">Nama
                                                Resep</label>
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('nama_resep') is-invalid @enderror"
                                                    id="floatingInput" placeholder="name@example.com" name="nama_resep">
                                                <label for="floatingInput">Nama Resep</label>
                                            </div>
                                            @error('nama_resep')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="inputState"
                                                class="form-label font-weight-bold text-primary">Deskripsi
                                                Resep</label>
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('keterangan') is-invalid @enderror"
                                                    id="floatingInput" placeholder="name@example.com" name="keterangan">
                                                <label for="floatingInput">Deskripsi Resep</label>
                                            </div>
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- </div> --}}
                                    <div class="card shadow mb-4">
                                        <!-- Card Header - Accordion -->
                                        <div href="#" class="d-block card-header py-3">
                                            <div class="row ">
                                                <div class="col-sm-5">
                                                    <h6 class="m-0 font-weight-bold text-primary">Nama Bahan</h6>
                                                </div>
                                                <div class="col-sm-3">
                                                    <h6 class="m-0 font-weight-bold text-primary">Satuan</h6>
                                                </div>
                                                <div class="col-sm-3">
                                                    <h6 class="m-0 font-weight-bold text-primary">Qty</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Card Content - Collapse -->
                                        <div class="collapse show" id="collapseCardExample">
                                            <div class="card-body">
                                                <div id="selectedProductsContainer">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="content" class="font-weight-bold text-primary">Instruksi</label>
                                        <textarea id="summernote" name="instruksi" class="form-control" rows="10" cols="50"></textarea>

                                    </div>
                                    <div class="row g-2">
                                        <div class="col-sm-6">
                                            <button class="btn btn-warning w-100" type="button"
                                                data-dismiss="modal">Cancel</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <button type="submit" class="btn btn-primary w-100">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-6">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3" id="product">
                                        <h6 class="m-0 font-weight-bold text-primary">List Bahan Baku</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($produks as $produk)
                                                <div class="col-sm-3">
                                                    <a href="#" class="btn produk-btn"
                                                        data-nama-barang="{{ $produk->nama_barang }}"
                                                        data-satuan="{{ $produk->satuan }}"
                                                        data-id-barang="{{ $produk->id }}">
                                                        <div class="card shadow" style="width: 110px;height:160px">
                                                            <div class="container  d-flex align-items-center justify-content-center"
                                                                style="width: 110px;height:110px;background-color:rgb(171, 170, 170)">
                                                                <h1 class="m-0 text-bold text-white">
                                                                    {{ strtoupper(substr($produk->nama_barang, 0, 1)) }}{{ strtoupper(substr($produk->nama_barang, strpos($produk->nama_barang, ' ') + 1, 1)) }}
                                                                </h1>
                                                            </div>
                                                            <span for="" class="text-center mt-2"
                                                                style="font-size: 12px">{{ $produk->nama_barang }}</span>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var productButtons = document.querySelectorAll('.produk-btn');
                var selectedProductsContainer = document.getElementById('selectedProductsContainer');

                productButtons.forEach(function(button) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        var productName = this.getAttribute('data-nama-barang');
                        var existingProductRow = document.querySelector(
                            `[data-product-name="${productName}"]`);

                        if (existingProductRow) {
                            // Jika produk sudah ada, tambahkan quantity
                            var qtyInput = existingProductRow.querySelector('.qty-input');
                            var currentQty = parseInt(qtyInput.value);
                            qtyInput.value = currentQty + 1;
                        } else {
                            // Jika produk belum ada, tambahkan row baru
                            var satuan = this.getAttribute('data-satuan');
                            var harga = this.getAttribute('data-harga');
                            var idBarang = this.getAttribute('data-id-barang');
                            addProductRow(productName, satuan, harga, idBarang);
                        }
                        updateTotal();
                    });
                });

                function addProductRow(productName, satuan, harga, idBarang) {
                    var productRow = document.createElement('div');
                    productRow.className = 'row mb-2';
                    productRow.setAttribute('data-product-name', productName);

                    var productNameDiv = document.createElement('div');
                    productNameDiv.className = 'col-sm-4';
                    productNameDiv.textContent = productName;

                    var satuanDiv = document.createElement('div');
                    satuanDiv.className = 'col-sm-3';
                    satuanDiv.textContent = satuan;

                    var qtyDiv = document.createElement('div');
                    qtyDiv.className = 'col-sm-3';
                    qtyDiv.innerHTML =
                        '<input type="number" class="form-control qty-input" name="qty[]" value="1" min="1" onchange="updateTotal()">';

                    var deleteButtonDiv = document.createElement('div');
                    deleteButtonDiv.className = 'col-sm-2';
                    deleteButtonDiv.innerHTML =
                        '<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>';
                    deleteButtonDiv.addEventListener('click', function(event) {
                        event.preventDefault();
                        selectedProductsContainer.removeChild(productRow);
                        updateTotal();
                    });

                    var hiddenProductNameInput = document.createElement('input');
                    hiddenProductNameInput.type = 'hidden';
                    hiddenProductNameInput.name = 'nama_barang[]';
                    hiddenProductNameInput.value = idBarang;

                    // Append all elements to the product row
                    productRow.appendChild(productNameDiv);
                    productRow.appendChild(satuanDiv);
                    productRow.appendChild(qtyDiv);
                    productRow.appendChild(deleteButtonDiv);
                    productRow.appendChild(hiddenProductNameInput);

                    selectedProductsContainer.appendChild(productRow);
                }

            });
        </script>

        <script>
            $('#summernote').summernote({
                placeholder: 'Masukan Instruksi Dari Resep',
                tabsize: 2,
                height: 100
            });
        </script>
    @endsection
