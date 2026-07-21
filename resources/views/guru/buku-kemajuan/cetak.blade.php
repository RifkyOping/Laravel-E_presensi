<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Monitoring Kelas - {{ $kelas }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .page-break {
            page-break-after: always;
        }

        .page {
            width: 21cm;
            /* A4 width */
            margin: 1cm auto;
            padding: 1cm;
            background: white;
            box-sizing: border-box;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
                /* Menghapus header dan footer bawaan browser (URL & Tanggal) */
            }

            body {
                background: none;
                margin: 0;
            }

            .page {
                margin: 0;
                padding: 1.5cm 1cm;
                /* Jarak aman agar teks tidak terpotong tepi kertas saat margin browser 0 */
                border: none;
                box-shadow: none;
                width: auto;
            }
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-text {
            flex: 1;
        }

        .header-text h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }

        .header-text h2 {
            margin: 2px 0;
            font-size: 16pt;
            font-weight: bold;
        }

        .header-text p {
            margin: 2px 0;
            font-size: 12pt;
            font-weight: bold;
        }

        .logo {
            width: 90px;
            height: auto;
            object-fit: contain;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .title p {
            margin: 2px 0;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 5px;
            vertical-align: top;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
            text-align: center;
        }

        .signature {
            width: 200px;
        }

        .signature p {
            margin: 0;
        }

        .signature-space {
            height: 70px;
        }
    </style>
</head>

<body onload="window.print()">

    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
        $mingguKe = 1;
        // Jika aktivitasPerMinggu kosong
        if ($aktivitasPerMinggu->isEmpty()) {
            echo "<div style='text-align:center; padding: 50px;'><h3>Tidak ada data mengajar di kelas {$kelas} pada rentang tanggal ini.</h3></div>";
        }
    @endphp

    @foreach($aktivitasPerMinggu as $awalMinggu => $aktivitasMingguan)
        <div class="page {{ !$loop->last ? 'page-break' : '' }}">

            <div class="header">
                <!-- Logo Kiri (Sulawesi Barat) -->
                <div style="width: 100px; text-align: left;">
                    <img src="{{ asset('images/logo sulbar.png') }}" class="logo" alt="Logo Sulbar"
                        onerror="this.style.display='none'">
                </div>

                <!-- Teks Tengah -->
                <div class="header-text">
                    <h3>PEMERINTAH PROVINSI SULAWESI BARAT</h3>
                    <h2>SEKOLAH MENENGAH KEJURUAN NEGERI 1 MAJENE</h2>
                    <p>Jl.KH.Muh.SALEH No.41 Tlp. (0422) 22978</p>
                    <p>Email: <a href="mailto:smkn1mjn@gmail.com"
                            style="color: blue; text-decoration: underline;">smkn1mjn@gmail.com</a>, Website :
                        smkn1majene.sch.id</p>
                </div>

                <!-- Logo Kanan (Sekolah) -->
                <div style="width: 100px; text-align: right;">
                    <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo SMKN 1 Majene"
                        onerror="this.style.display='none'">
                </div>
            </div>

            <div class="title">
                <p style="font-size: 14pt;">MONITORING KELAS</p>
                <p style="font-size: 12pt;">TAHUN PELAJARAN {{ \App\Models\SchoolSetting::get()->tahun_ajaran }}</p>
            </div>

            <div class="meta" style="font-size: 12pt;">
                <div>KELAS : {{ $kelas }}</div>
                <div>SEMESTER: {{ request('semester', '..........') }}</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="3%">NO</th>
                        <th width="12%">HARI / TGL</th>
                        <th width="10%">PUKUL</th>
                        <th width="15%">MATA PELAJARAN</th>
                        <th width="15%">NAMA GURU</th>
                        <th width="25%">TUJUAN PEMBELAJARAN</th>
                        <th width="10%">PARAF</th>
                        <th width="10%">KET</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Kelompokkan aktivitas dalam minggu ini berdasarkan Hari/Tanggal
                        $aktivitasPerHari = $aktivitasMingguan->groupBy('tanggal');
                        $noHari = 1;
                    @endphp

                    @foreach($aktivitasPerHari as $tanggal => $kegiatans)
                        @php
                            $jmlKegiatan = $kegiatans->count();
                            $tglCarbon = Carbon::parse($tanggal);
                            $hariTgl = strtoupper($tglCarbon->translatedFormat('l')) . " /<br>" . $tglCarbon->format('d-m-Y');
                        @endphp

                        @foreach($kegiatans as $index => $kegiatan)
                            <tr>
                                @if($index === 0)
                                    <td class="text-center" rowspan="{{ $jmlKegiatan }}">{{ $noHari++ }}.</td>
                                    <td rowspan="{{ $jmlKegiatan }}">{!! $hariTgl !!}</td>
                                @endif
                                <td class="text-center">{{ $kegiatan->jam_mulai ?? $kegiatan->jam_ke }} -
                                    {{ $kegiatan->jam_selesai ?? 'Selesai' }}</td>
                                <td>{{ $kegiatan->mata_pelajaran }}</td>
                                <td>{{ $kegiatan->user->name }}</td>
                                <td>{{ $kegiatan->materi_pembelajaran ?: '-' }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="footer">
                <div class="signature">
                    <p>Majene, ...........................</p>
                    <p>Wali Kelas</p>
                    <div class="signature-space"></div>
                    <p>(...................................)</p>
                </div>
            </div>

        </div>
    @endforeach

</body>

</html>