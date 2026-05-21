<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.3; }
        
        /* Tabel Header (Tanpa Border) */
        .table-header { width: 100%; border: none; margin-bottom: 20px; }
        .table-header td { border: none; vertical-align: middle; }
        .header-text h4 { margin: 0; padding: 0; font-size: 14pt; }
        .header-text p { margin: 2px 0; font-size: 9pt; }
        .logo-container { text-align: right; }

        /* Tabel Data (Dengan Border) */
        .table-border { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-border th, 
        .table-border td { border: 1px solid black; padding: 6px; vertical-align: middle; }
        .table-border th { background-color: #f2f2f2; text-align: center; font-size: 9pt; }
        
        .center { text-align: center; }
        .signature-container { margin-top: 30px; float: right; width: 250px; text-align: left; }
    </style>
</head>
<body>
    <table class="table-header">
        <tr>
            <td class="header-text">
                <h4>POLITEKNIK NEGERI CILACAP</h4>
                <p>Jl. Dr.Soetomo No.1 Sidakaya, CILACAP 53212, Jawa Tengah</p>
                <p>E-mail: sekretariat@pnc.ac.id, Website: www.pnc.ac.id</p>
                <p>Telp: (0282) 533329 Fax: (0282) 537992</p>
            </td>
            <td class="logo-container">
                <img style="width: 120px" src="{{ public_path('img/logo_pnc.png') }}" alt="Logo PNC">
            </td>
        </tr>
    </table>

    <hr style="border: 1px solid black; margin-bottom: 10px;">

    <div class="center" style="margin-bottom: 15px;">
        <strong>RINGKASAN KONDISI AUDIT</strong> <br>
    </div>

    <table>
        <tr>
            <td style="width: 30%; margin-right:2px;">Kriteria</td>
            <td style="margin-left:5px;">: Standar SPMI</td>
        </tr>
        <tr>
            <td style="width: 30%; margin-right:2px;">Tgl Penilaian</td>
            <td style="margin-left:5px;">: {{$penugasan->tanggal_audit}} - {{$penugasan->jam}}</td>
        </tr>
        <tr>
            <td style="width: 30%; margin-right:2px;">Auditi</td>
            @php
                if($upt->kategori_upt == 'Prodi'){
                    $kategori = 'Program Studi';
                }else{
                    $kategori = 'Unit';
                }
            @endphp
            <td>: 
                {{$kategori}}  
                {{$upt->nama_upt}}
            </td>
        </tr>
        <tr>
            <td style="width: 30%; margin-right:2px;">Auditor (Ketua)</td>
            <td style="margin-left:5px;">: {{$penugasan->auditor1->nama_lengkap}}</td>
        </tr>
        <tr>
            <td style="width: 30%; margin-right:2px;">Auditor (Anggota)</td>
            <td style="margin-left:5px;">: {{$penugasan->auditor2->nama_lengkap}}</td>
        </tr>
    </table>

    <table class="table-border">
        <thead>
            <tr>
                <th style="width: 5%;">NAMA STANDAR</th>
                <th style="width: 18%;">DESKRIPSI KONDISI</th>
                <th style="width: 3%;">KATEGORI <br>(OB/KTS)</th>
            </tr>
        </thead>
<tbody>
    @foreach($standarMutu as $s)
    @php
        $temuan = [];
        
        foreach ($s->subStandarUpt ?? [] as $sub) {
            foreach ($sub->items ?? [] as $item) {
                // Cek apakah item ini memiliki jawaban_audit
                if ($item->jawaban_audit) {
                    $temuan[] = [
                        'catatan' => $item->jawaban_audit->catatan,
                        // PERUBAHAN DI SINI: Mengambil kategori_temuan dari jawaban_audit
                        'kategori' => $item->jawaban_audit->kategori_temuan ?? '-' 
                    ];
                }
            }
        }
        
        // Hilangkan duplikasi jika ada catatan dan kategori yang sama persis dalam satu standar
        $temuan = array_map("unserialize", array_unique(array_map("serialize", $temuan)));
        $jumlahTemuan = count($temuan);
    @endphp

        @if($jumlahTemuan > 0)
            @foreach($temuan as $index => $t)
                <tr>
                    @if($index == 0)
                        {{-- Nama Standar muncul hanya sekali di baris pertama temuan --}}
                        <td rowspan="{{ $jumlahTemuan }}">
                            {{ $s->standar_mutu->nama_standar_mutu }}
                        </td>
                    @endif
                    <td>{{ $t['catatan'] }}</td>
                    <td class="center">{{ $t['kategori'] }}</td>
                </tr>
            @endforeach
        @else
            {{-- Jika tidak ada jawaban = 0, Standar tetap muncul dengan kolom deskripsi kosong --}}
            <tr>
                <td>{{ $s->standar_mutu->nama_standar_mutu }}</td>
                <td>-</td>
                <td class="center">-</td>
            </tr>
        @endif
    @endforeach
</tbody>
    </table>

    <div class="signature-container">
        Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} <br>
        Kepala PPMRP, <br><br><br><br>
        <strong>Artdhita Fajar Pratiwi, S.T., M.Eng.</strong><br>
        NIP. 198506242019032013
    </div>
</body>
</html>