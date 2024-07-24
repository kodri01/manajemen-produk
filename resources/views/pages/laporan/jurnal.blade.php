@extends('layouts.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $judul }}</h1>
    </div>

    <form action="{{ route('lap.neraca') }}" method="GET">
        <div class="wrapper-table bg-white rounded ">
            <div class="card shadow ">
                <div class="card-body">
                    <div class="card">
                        <div class="card-header text-center">
                            <h6 class="text-uppercase "><b>{{ $setting->company_name }}</b></h6>
                            <h6 class="text-capitalize"><b>Laporan Jurnal Umum</b></h6>
                            {{-- <h6 class="text-capitalize"><b>Periode Akhir Desember {{ $selectedYear }}</b></h6> --}}
                        </div>
                        <div class="card-body">
                            <table class="table table-responsive-lg">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Nomor Jurnal</th>
                                        <th scope="col">Keterangan</th>
                                        <th scope="col">Akun Debet</th>
                                        <th scope="col">Debit</th>
                                        <th scope="col">Akun Kredit</th>
                                        <th scope="col">Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($laporans as $lap)
                                        <tr>
                                            <td>{{ date('d/M/Y', strtotime($lap->created_at)) }}</td>
                                            <td>{{ $lap->no_jurnal }}</td>
                                            <td>{{ $lap->ket }}</td>
                                            <td>{{ $lap->akun_debet }}</td>
                                            <td class="text-success">
                                                {{ 'Rp ' . number_format($lap->debit, 0, ',', '.') }}
                                            </td>
                                            <td>{{ $lap->akun_kredit }}</td>
                                            <td class="text-danger">
                                                {{ 'Rp ' . number_format($lap->kredit, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
