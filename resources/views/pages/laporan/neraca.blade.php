@extends('layouts.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $judul }}</h1>
    </div>

    <form action="{{ route('lap.neraca') }}" method="GET">
        <div class="wrapper-table bg-white rounded ">
            <div class="card shadow ">
                <div class="card-header">
                    <div class="input-group flex-nowrap w-25">
                        <select id="tahun" name="tahun" class="btn btn-primary">
                            <option selected disabled class="text-white">-- Pilih Tahun --</option>
                            @foreach ($tahun as $tahunItem)
                                <option value="{{ $tahunItem }}">{{ $tahunItem }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <div class="card-header text-center">
                            <h6 class="text-uppercase "><b>{{ $setting->company_name }}</b></h6>
                            <h6 class="text-capitalize"><b>laporan posisi keuangan (neraca)</b></h6>
                            <h6 class="text-capitalize"><b>Periode Akhir Desember {{ $selectedYear }}</b></h6>
                        </div>
                        <div class="card-body">
                            <div class="row ">
                                <div class="col-sm-6 ">
                                    <center> <span class="text-uppercase"><b>aktiva</b></span></center>
                                    <span class="text-capitalize"><b>aktiva lancar</b></span>
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <span>Kas</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span>{{ 'Rp ' . number_format($kas, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>Jumlah</b></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>{{ 'Rp ' . number_format($kas, 0, ',', '.') }}</b></span>
                                        </div>
                                    </div>
                                    <span class="text-capitalize"><b>aktiva Tetap</b></span>
                                    <div class="row mb-3">
                                        {{-- <div class="col-sm-6">
                                        <span>Piutang Usaha</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>Rp. 200.000</span>
                                    </div> --}}
                                        <div class="col-sm-6">
                                            <span>Persediaan</span>
                                        </div>
                                        {{-- <div class="col-sm-6">
                                        <span>Rp. 200.000</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>Beban Dibayar Dimuka</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>Rp. 200.000</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>Aset Tetap</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>Rp. 200.000</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span>Akumulasi Penyusutan</span>
                                    </div> 
                                    <div class="col-sm-6">
                                        <span><u class="mt-2">
                                                Rp. 200.000</u>
                                            <sub>+</sub>
                                        </span>
                                    </div> --}}
                                        <div class="col-sm-6">
                                            <span>{{ 'Rp ' . number_format($persediaan, 0, ',', '.') }}</span>

                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>Jumlah</b></span>
                                        </div>
                                        <div class="col-sm-6 ">
                                            <span><u class="mt-2">
                                                    <b>{{ 'Rp ' . number_format($persediaan, 0, ',', '.') }}</b></u>
                                                <sub>+</sub>
                                            </span>
                                        </div>
                                    </div>
                                    {{-- <hr style="margin-left:17.7rem;height: 1px; width:120px;background: #757272;"> --}}
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <span><b>Total Aktiva (Lancar + Tetap)</b></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>{{ 'Rp ' . number_format($totalAktiva, 0, ',', '.') }}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <center> <span class="text-uppercase"><b>kewajiban</b></span></center>
                                    <span class="text-capitalize"><b>liabilitas</b></span>
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <span>Utang Usaha</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span>{{ 'Rp ' . number_format($utangUsaha, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span>Utang Bank</span>
                                        </div>

                                        <div class="col-sm-6">
                                            <span><u class="mt-2">
                                                    {{ 'Rp ' . number_format($utangBank, 0, ',', '.') }}</u>
                                                <sub>+</sub>
                                            </span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>Jumlah Liabilitas</b></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>{{ 'Rp ' . number_format($totalUtang, 0, ',', '.') }}</b></span>
                                        </div>
                                    </div>
                                    <span class="text-capitalize"><b>ekuitas</b></span>
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <span>Modal</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span>{{ 'Rp ' . number_format($modal, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span>Saldo Laba (Rugi)</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><u class="mt-2">
                                                    {{ 'Rp ' . number_format($labaRugi, 0, ',', '.') }}</u>
                                                <sub>+</sub>
                                            </span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>Jumlah</b></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span>
                                                <b>{{ 'Rp ' . number_format($totalEkuitas, 0, ',', '.') }}</b>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-6">
                                            <span><b>Total (Liabilitas + Ekuitas)</b></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <span><b>{{ 'Rp ' . number_format($totalKewajiban, 0, ',', '.') }}</b></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil elemen dropdown tahun
            var dropdownTahun = document.getElementById('tahun');

            // Tambahkan event listener untuk perubahan nilai dropdown
            dropdownTahun.addEventListener('change', function() {
                // Submit form saat nilai dropdown berubah
                this.closest('form').submit();
            });
        });
    </script>
@endsection
