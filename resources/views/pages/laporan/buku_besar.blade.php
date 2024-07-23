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
                            <h6 class="text-capitalize"><b>laporan buku besar</b></h6>
                        </div>
                        <div class="card-body">
                            <div class="card p-2">
                                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active btn-sm" id="pills-home-tab" data-toggle="pill"
                                            data-target="#pills-home" type="button" role="tab"
                                            aria-controls="pills-home" aria-selected="true">Kas</button>
                                    </li>
                                    <li class="nav-item mx-3" role="presentation">
                                        <button class="nav-link btn-sm" id="pills-profile-tab" data-toggle="pill"
                                            data-target="#pills-profile" type="button" role="tab"
                                            aria-controls="pills-profile" aria-selected="false">Pendapatan</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link btn-sm" id="pills-contact-tab" data-toggle="pill"
                                            data-target="#pills-contact" type="button" role="tab"
                                            aria-controls="pills-contact" aria-selected="false">Beban</button>
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <div class="tab-content" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                            aria-labelledby="pills-home-tab">
                                            <table class="table table-responsive-lg">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th scope="col">Tanggal</th>
                                                        <th scope="col">Keterangan</th>
                                                        <th scope="col">Referensi</th>
                                                        <th scope="col">Debit</th>
                                                        <th scope="col">Kredit</th>
                                                        <th scope="col">Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $modalAwal = 6000000; // Nilai modal awal
                                                        $masterName = $masters->pluck('name')->toArray();
                                                    @endphp
                                                    <tr>
                                                        <td>01/01/2024</td>
                                                        <td>Saldo Awal</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ 'Rp ' . number_format($modalAwal, 0, ',', '.') }}</td>
                                                    </tr>
                                                    @foreach ($laporans as $lap)
                                                        @php
                                                            // Update nilai modal berdasarkan debit atau kredit
                                                            if ($lap->akun_debet == 'Penjualan') {
                                                                $modalAwal += $lap->debit;
                                                            } elseif (
                                                                $lap->akun_kredit == 'Kas' ||
                                                                in_array($lap->akun_kredit, $masterName)
                                                            ) {
                                                                $modalAwal -= $lap->kredit;
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ date('d/M/Y', strtotime($lap->created_at)) }}</td>
                                                            <td>{{ $lap->ket }}</td>
                                                            <td>{{ $lap->no_jurnal }}</td>
                                                            @if ($lap->akun_debet == 'Penjualan')
                                                                <td>{{ 'Rp ' . number_format($lap->debit, 0, ',', '.') }}
                                                                </td>
                                                                <td>Rp. 0</td>
                                                            @elseif ($lap->akun_kredit == 'Kas' || in_array($lap->akun_kredit, $masterName))
                                                                <td>Rp. 0</td>
                                                                <td>{{ 'Rp ' . number_format($lap->kredit, 0, ',', '.') }}
                                                                </td>
                                                            @endif
                                                            <td>{{ 'Rp ' . number_format($modalAwal, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                            aria-labelledby="pills-profile-tab">
                                            <table class="table table-responsive-lg">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th scope="col">Tanggal</th>
                                                        <th scope="col">Keterangan</th>
                                                        <th scope="col">Referensi</th>
                                                        <th scope="col">Debit</th>
                                                        <th scope="col">Kredit</th>
                                                        <th scope="col">Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $modalAwal = 0; // Nilai modal awal
                                                    @endphp
                                                    <tr>
                                                        <td>01/01/2024</td>
                                                        <td>Saldo Awal</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ 'Rp ' . number_format($modalAwal, 0, ',', '.') }}</td>
                                                    </tr>
                                                    @foreach ($penjualan as $lap)
                                                        @php
                                                            // Update nilai modal berdasarkan debit atau kredit
                                                            if ($lap->akun_debet == 'Penjualan') {
                                                                $modalAwal += $lap->debit;
                                                            } elseif ($lap->akun_kredit == 'Kas') {
                                                                $modalAwal -= $lap->kredit;
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td>{{ date('d/M/Y', strtotime($lap->created_at)) }}</td>
                                                            <td>{{ $lap->ket }}</td>
                                                            <td>{{ $lap->no_jurnal }}</td>
                                                            @if ($lap->akun_debet == 'Penjualan')
                                                                <td>Rp. 0</td>
                                                                <td>{{ 'Rp ' . number_format($lap->debit, 0, ',', '.') }}
                                                                </td>
                                                            @endif

                                                            <td>{{ 'Rp ' . number_format($modalAwal, 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                            aria-labelledby="pills-contact-tab">
                                            <table class="table table-responsive-lg">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th scope="col">Tanggal</th>
                                                        <th scope="col">Keterangan</th>
                                                        <th scope="col">Referensi</th>
                                                        <th scope="col">Debit</th>
                                                        <th scope="col">Kredit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach ($beban as $lap)
                                                        <tr>
                                                            <td>{{ date('d/M/Y', strtotime($lap->created_at)) }}</td>
                                                            <td>{{ $lap->ket }}</td>
                                                            <td>{{ $lap->no_jurnal }}</td>
                                                            <td>Rp. 0</td>
                                                            <td>{{ 'Rp ' . number_format($lap->kredit, 0, ',', '.') }}
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
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            $('#pills-tab button').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@endsection
