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
        <strong>JADWAL AUDIT MUTU INTERNAL</strong> <br>
        <strong>TAHUN {{ $tahun }}</strong>
    </div>

    <table class="table-border">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">NO</th>
                <th rowspan="2" style="width: 18%;">HARI/TANGGAL</th>
                <th rowspan="2" style="width: 10%;">WAKTU</th>
                <th rowspan="2" style="width: 22%;">AUDITI</th>
                <th colspan="2" style="width: 45%;">AUDITOR</th>
            </tr>
            <tr>
                <th>KETUA</th>
                <th>ANGGOTA</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $allData = $uptProdi->concat($uptBagian); 
                $iteration = 1;
            @endphp
            @foreach ($allData as $item)
                @php 
                    // Mengambil data penugasan pertama dari relasi penugasan milik UPT
                    $info = $item->penugasan->first();
                @endphp
                <tr>
                    <td class="center">{{ $iteration++ }}</td>
                    <td>
                        {{ $info && $info->tanggal_audit ? \Carbon\Carbon::parse($info->tanggal_audit)->translatedFormat('l, d-m-Y') : '-' }}
                    </td>
                    <td class="center">
                        {{ $info && $info->jam ? \Carbon\Carbon::parse($info->jam)->format('H:i') . ' WIB' : '-' }}
                    </td>
                    <td>{{ $item->nama_upt }}</td>
                    
                    {{-- Perbaikan: Memanggil auditor1 & auditor2 dari variabel $info --}}
                    <td>{{ $info && $info->auditor1 ? $info->auditor1->nama_lengkap : '-' }}</td>
                    <td>{{ $info && $info->auditor2 ? $info->auditor2->nama_lengkap : '-' }}</td>
                </tr>
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