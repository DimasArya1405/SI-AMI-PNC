<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 14mm 13mm;
        }

        body {
            color: #1f1f1f;
            font-family: Arial, sans-serif;
            font-size: 10.5pt;
            line-height: 1.25;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .page {
            border: 1.5px solid #444;
            padding: 8px 10px 10px;
            width: 100%;
        }

        .top-header td {
            border: 1.4px solid #444;
            padding: 4px 6px;
        }

        .institution {
            border-right: 0 !important;
            height: 56px;
        }

        .institution h1 {
            font-size: 13pt;
            line-height: 1.05;
            margin: 0 0 2px;
        }

        .institution p {
            font-size: 9.5pt;
            line-height: 1.1;
            margin: 1px 0;
        }

        .logo-cell {
            border-left: 0 !important;
            text-align: right;
            vertical-align: middle;
            width: 230px;
        }

        .logo-cell img {
            width: 205px;
        }

        .main-box {
            border: 1.4px solid #444;
            margin-top: 10px;
        }

        .main-box td,
        .main-box th {
            border: 0;
            padding: 5px 6px;
        }

        .title {
            font-size: 13pt;
            font-weight: bold;
            height: 38px;
            letter-spacing: .2px;
            text-align: center;
        }

        .meta-label {
            width: 135px;
        }

        .meta-separator {
            text-align: center;
            width: 15px;
        }

        .meta-value {
            min-height: 19px;
        }

        .edition-box {
            border: 1.4px solid #444 !important;
            padding: 0 !important;
            width: 135px;
        }

        .edition-table td {
            border: 1.2px solid #666;
            font-size: 10pt;
            padding: 6px;
        }

        .edition-title {
            background: #dbe5f1;
            font-weight: bold;
            text-align: left;
        }

        .radio {
            font-size: 16pt;
            line-height: 1;
            vertical-align: middle;
        }

        .audit-table {
            border: 1.4px solid #444;
            margin-top: -1px;
        }

        .audit-table th,
        .audit-table td {
            border: 1.2px solid #444;
            padding: 6px 7px;
            vertical-align: middle;
        }

        .audit-table th {
            background: #dbe5f1;
            font-size: 11pt;
            font-style: italic;
            font-weight: bold;
            text-align: center;
        }

        .standard-col {
            text-align: center;
            width: 14%;
        }

        .condition-col {
            width: 74%;
        }

        .category-col {
            text-align: center;
            width: 12%;
        }

        .condition-text {
            min-height: 30px;
            text-align: left;
        }

        .definition {
            font-size: 9.5pt;
            font-style: italic;
            margin: 9px 0 0;
        }

        .definition p {
            margin: 3px 0;
        }

        .approval {
            border: 1.4px solid #444;
            margin-top: 12px;
        }

        .approval td {
            border: 1.2px solid #444;
            padding: 5px 6px;
            text-align: center;
            vertical-align: middle;
        }

        .approval-title {
            background: #dbe5f1;
            font-size: 11pt;
            font-weight: bold;
            height: 20px;
        }

        .role {
            width: 14%;
        }

        .person {
            width: 24%;
        }

        .ttd {
            height: 48px;
            text-align: left !important;
            vertical-align: top !important;
            width: 12%;
        }

        .review-role {
            width: 36%;
        }

        .review-name {
            width: 28%;
        }

        .review-sign {
            height: 58px;
            text-align: left !important;
            vertical-align: top !important;
        }
    </style>
</head>
<body>
@php
    $kepalaP4mp = Auth::user()?->role === 'kepala_p4mp'
        ? Auth::user()
        : \App\Models\User::where('role', 'kepala_p4mp')->first();
    $namaKepalaP4mp = $kepalaP4mp?->name ?? 'Kepala P4MP';
    $kategori = ($upt?->kategori_upt ?? null) === 'Prodi' ? 'Program Studi' : 'Unit';
    $tanggalAudit = $penugasan->tanggal_audit
        ? \Illuminate\Support\Carbon::parse($penugasan->tanggal_audit)->translatedFormat('j F Y')
        : '-';
    $logoSrc = null;
    $logoCandidates = [
        public_path('img/logo_pnc.png'),
        public_path('img/logo-pnc-1.png'),
        public_path('img/pnc.png'),
    ];

    foreach ($logoCandidates as $logoPath) {
        if (is_readable($logoPath)) {
            $extension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime = $extension === 'jpg' || $extension === 'jpeg' ? 'image/jpeg' : 'image/png';
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            break;
        }
    }

    if (isset($rka)) {
        $temuanPerStandar = $rka->temuan
            ->sortBy(function ($temuan) {
                $item = $temuan->jawabanAudit?->itemSubStandar;
                $standar = $item?->uptSubStandar?->uptStandarMutu?->standar_mutu;

                return sprintf(
                    '%05d-%05d-%05d',
                    $standar?->urutan ?? 0,
                    $item?->uptSubStandar?->urutan ?? 0,
                    $item?->urutan ?? 0
                );
            })
            ->groupBy(fn ($temuan) => $temuan->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->standar_mutu_id ?? 'tanpa-standar');
    }
@endphp

<div class="page">
    <table class="top-header">
        <tr>
            <td class="institution">
                <h1>POLITEKNIK NEGERI CILACAP</h1>
                <p>Jl.Dr. Soetomo No.1 Sidakaya, CILACAP 53212, Jawa Tengah</p>
                <p>E-mail: sekretariat@pnc.ac.id, Website: www.pnc.ac.id</p>
                <p>Telp : (0282) 537992 Fax : (0282) 533329</p>
            </td>
            <td class="logo-cell">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo PNC">
                @endif
            </td>
        </tr>
    </table>

    <table class="main-box">
        <tr>
            <td colspan="3" class="title">RINGKASAN KONDISI AUDIT</td>
        </tr>
        <tr>
            <td class="meta-label">Kriteria</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">Standar SPMI PNC</td>
        </tr>
        <tr>
            <td class="meta-label">Tgl Penilaian</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $tanggalAudit }}</td>
        </tr>
        <tr>
            <td class="meta-label">Auditi</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $kategori }} {{ $upt?->nama_upt ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Auditor</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">1. {{ $penugasan->auditor1?->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label"></td>
            <td class="meta-separator"></td>
            <td class="meta-value">2. {{ $penugasan->auditor2?->nama_lengkap ?? '-' }}</td>
        </tr>
    </table>

    <table class="audit-table">
        <thead>
            <tr>
                <th class="standard-col">Nama<br>Standar</th>
                <th class="condition-col">Deskripsi Kondisi</th>
                <th class="category-col">Kategori<br>(OB/KTS)*</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($rka))
                @forelse($temuanPerStandar as $temuanStandar)
                    @php
                        $standar = $temuanStandar->first()?->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu;
                        $jumlahTemuan = $temuanStandar->count();
                    @endphp

                    @foreach($temuanStandar as $index => $temuan)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $jumlahTemuan }}" class="standard-col">
                                    {{ $standar?->nama_standar_mutu ?? '-' }}
                                </td>
                            @endif
                            <td class="condition-text">{{ $temuan->kondisi_final }}</td>
                            <td class="category-col">{{ $temuan->kategori_final }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td class="standard-col">-</td>
                        <td class="condition-text">Tidak ada temuan pada RKA.</td>
                        <td class="category-col">-</td>
                    </tr>
                @endforelse
            @else
                @foreach($standarMutu as $s)
                    @php
                        $temuan = [];

                        foreach ($s->subStandarUpt ?? [] as $sub) {
                            foreach ($sub->items ?? [] as $item) {
                                if ($item->jawaban_audit) {
                                    $temuan[] = [
                                        'catatan' => $item->jawaban_audit->catatan,
                                        'kategori' => $item->jawaban_audit->kategori_temuan ?? '-'
                                    ];
                                }
                            }
                        }

                        $temuan = array_map("unserialize", array_unique(array_map("serialize", $temuan)));
                        $jumlahTemuan = count($temuan);
                    @endphp

                    @if($jumlahTemuan > 0)
                        @foreach($temuan as $index => $t)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ $jumlahTemuan }}" class="standard-col">
                                        {{ $s->standar_mutu->nama_standar_mutu }}
                                    </td>
                                @endif
                                <td class="condition-text">{{ $t['catatan'] }}</td>
                                <td class="category-col">{{ $t['kategori'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="standard-col">{{ $s->standar_mutu->nama_standar_mutu }}</td>
                            <td class="condition-text">-</td>
                            <td class="category-col">-</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="definition">
        <p><strong>*OB (Observasi) :</strong> KTS ringan atau kondisi yang tidak langsung mempengaruhi mutu, mudah diralat, dan tidak menghambat akreditasi/sertifikasi. (Tindakan koreksi bersifat opsional)</p>
        <p><strong>*KTS (Ketidaksesuaian) :</strong> Kondisi yang berpengaruh besar terhadap mutu produk/pelayanan, menyebabkan risiko kehilangan konsumen, mengancam akreditasi/sertifikasi, ancaman thd kegiatan dalam organisasi, serta menyebabkan potensi pidana/perdata. (Tindakan koreksi bersifat wajib)</p>
    </div>

    <table class="approval">
        <tr>
            <td colspan="6" class="approval-title">Persetujuan</td>
        </tr>
        <tr>
            <td class="role">Ketua<br>Auditor</td>
            <td class="person">{{ $penugasan->auditor1?->nama_lengkap ?? '' }}</td>
            <td class="ttd">Ttd.</td>
            <td class="role">Auditor<br>Anggota</td>
            <td class="person">{{ $penugasan->auditor2?->nama_lengkap ?? '' }}</td>
            <td class="ttd">Ttd.</td>
        </tr>
        <tr>
            <td colspan="6" class="approval-title">Direview oleh:</td>
        </tr>
        <tr>
            <td colspan="2" class="review-role">Kepala P4MP</td>
            <td colspan="2" class="review-name">{{ $namaKepalaP4mp }}</td>
            <td colspan="2" class="review-sign">Ttd.</td>
        </tr>
    </table>
</div>
</body>
</html>
