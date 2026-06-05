<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 13mm 12mm;
        }

        body {
            color: #222;
            font-family: Arial, sans-serif;
            font-size: 10.5pt;
            line-height: 1.3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .sheet {
            border: 1.5px solid #444;
            padding: 8px 10px 10px;
        }

        .page-break {
            page-break-before: always;
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

        .doc-strip td {
            border: 1.4px solid #444;
            height: 15px;
            padding: 0;
        }

        .stripe {
            background: repeating-linear-gradient(
                -45deg,
                #444 0,
                #444 2px,
                #fff 2px,
                #fff 5px
            );
        }

        .doc-code {
            font-size: 8pt;
            font-style: italic;
            font-weight: bold;
            text-align: center;
            width: 220px;
        }

        .title {
            font-size: 13pt;
            font-weight: bold;
            margin: 12px 0 22px;
            text-align: center;
        }

        .source {
            font-size: 12pt;
            margin: 0 0 22px 25px;
        }

        .source td {
            padding: 5px 0;
        }

        .source-label {
            width: 190px;
        }

        .section-table {
            margin-top: 12px;
        }

        .section-table th {
            background: #dbe5f1;
            border: 1.25px solid #444;
            font-size: 10.5pt;
            padding: 6px;
            text-align: left;
        }

        .section-table td {
            border: 1.25px solid #444;
            padding: 6px;
            vertical-align: top;
        }

        .content-cell {
            width: 75%;
        }

        .sign-cell {
            font-size: 10.5pt;
            height: 82px;
            width: 25%;
        }

        .section-large .content-cell {
            height: 185px;
        }

        .section-medium .content-cell {
            height: 145px;
        }

        .section-small .content-cell {
            height: 125px;
        }

        .sign-space {
            height: 32px;
        }

        .section-list {
            margin: 0;
            padding-left: 18px;
        }

        .section-list li {
            font-style: italic;
            margin-bottom: 4px;
        }

        .verification-note {
            font-style: italic;
            margin: 0 0 8px;
        }

        .verification-detail {
            font-style: italic;
            margin: 4px 0 0;
            padding-left: 18px;
        }

        .verification-detail li {
            margin-bottom: 3px;
        }

        .unit-row {
            border-top: 1.25px solid #444;
            font-size: 11pt;
            margin: 10px -6px -6px;
            padding: 7px 6px 0;
            text-align: center;
        }

        .empty-text {
            color: #555;
            font-style: italic;
        }

        .notes {
            font-size: 10.5pt;
            font-style: italic;
            margin: 20px 0 0 7px;
        }

        .notes p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
@php
    $kategori = ($upt?->kategori_upt ?? null) === 'Prodi' ? 'PRODI' : 'UNIT';
    $unitKerja = trim($kategori . ' ' . ($upt?->nama_upt ?? '-'));
    $auditeeTerkait = \App\Models\Auditee::with('user')
        ->where('upt_id', $penugasan->upt_id)
        ->where('status_aktif', 1)
        ->first()
        ?? \App\Models\Auditee::with('user')->where('upt_id', $penugasan->upt_id)->first();
    $namaAuditee = $penugasan->auditee?->nama_lengkap
        ?? $penugasan->auditee?->user?->name
        ?? $auditeeTerkait?->nama_lengkap
        ?? $auditeeTerkait?->user?->name
        ?? $unitKerja;
    $tanggalAudit = $penugasan->tanggal_audit
        ? \Illuminate\Support\Carbon::parse($penugasan->tanggal_audit)->translatedFormat('j F Y')
        : '-';
    $tkItems = $temuan->filter(fn ($jawaban) => $jawaban->tindakanKoreksi)->values();
    $verifikasiTk = $penugasan->verifikasiTindakanKoreksi;
    $catatanUmumVerifikasi = trim((string) $verifikasiTk?->catatan_umum);
    $catatanUmumVerifikasi = preg_replace('/^\s*(\d+\.\s*)?Temuan\s+\d+\s+-\s*[^:]+:\s*/mi', '$1', $catatanUmumVerifikasi);
    $catatanItemVerifikasi = $tkItems
        ->map(fn ($jawaban) => trim((string) $jawaban->tindakanKoreksi?->p4mp_catatan))
        ->filter()
        ->values();
    $tanggalRka = optional($temuan->first()?->rkaTemuan?->rka?->tanggal_rapat)->translatedFormat('j F Y');
    $tanggalRka = $tanggalRka ?: $tanggalAudit;
    $tanggalRumusan = optional($tkItems->first()?->tindakanKoreksi?->created_at)->translatedFormat('j F Y') ?: $tanggalAudit;
    $tanggalPelaksanaan = optional($tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->tanggal_penilaian_ulang)?->tindakanKoreksi?->tanggal_penilaian_ulang)->translatedFormat('j F Y') ?: '-';
    $tanggalP4mp = $verifikasiTk?->finalized_at
        ? $verifikasiTk->finalized_at->translatedFormat('j F Y')
        : (optional($tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->p4mp_verified_at)?->tindakanKoreksi?->p4mp_verified_at)->translatedFormat('j F Y') ?: '-');
    $namaP4mp = $verifikasiTk?->finalizedBy?->name
        ?? $tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->p4mpVerifiedBy)?->tindakanKoreksi?->p4mpVerifiedBy?->name
        ?? 'Kepala P4MP';
    $namaWadir = $verifikasiTk?->wadir1_nama
        ?? $tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->wadir1_nama)?->tindakanKoreksi?->wadir1_nama
        ?? 'Wadir I';
@endphp

<div class="sheet">
    <table class="top-header">
        <tr>
            <td class="institution">
                <h1>POLITEKNIK NEGERI CILACAP</h1>
                <p>Jl.Dr. Soetomo No.1 Sidakaya, CILACAP 53212, Jawa Tengah</p>
                <p>E-mail: sekretariat@pnc.ac.id, Website: www.pnc.ac.id</p>
                <p>Telp : (0282) 537992 Fax : (0282) 533329</p>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('img/logo_pnc.png') }}" alt="Logo PNC">
            </td>
        </tr>
    </table>

    <table class="doc-strip">
        <tr>
            <td class="stripe"></td>
            <td class="doc-code">FM.SOP 1/TK-Q.02-01</td>
        </tr>
    </table>

    <div class="title">FORMULIR USULAN TINDAKAN KOREKSI</div>

    <table class="section-table section-large">
        <tr>
            <th colspan="2">Temuan/Laporan Ketidaksesuaian :</th>
        </tr>
        <tr>
            <td rowspan="2" class="content-cell">
                <ol class="section-list">
                    @forelse ($temuan as $jawaban)
                        <li>{{ $jawaban->rkaTemuan?->kondisi_final ?: ($jawaban->catatan ?: $jawaban->itemSubStandar?->nama_item ?? '-') }}</li>
                    @empty
                        <li class="empty-text">Belum ada temuan tindakan koreksi.</li>
                    @endforelse
                </ol>
                <div class="unit-row">Di Bagian / Unit Kerja : <strong>{{ $unitKerja }}</strong></div>
            </td>
            <td class="sign-cell">
                Auditor<br>
                Tgl : {{ $tanggalRka }}
                <div class="sign-space"></div>
                ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Auditor<br>
                Tgl : {{ $tanggalRka }}
                <div class="sign-space"></div>
                ( {{ $penugasan->auditor2?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
    </table>

    <table class="section-table section-large">
        <tr>
            <th colspan="2">Analisa Ketidaksesuaian dan Penyebabnya :</th>
        </tr>
        <tr>
            <td rowspan="2" class="content-cell">
                <ol class="section-list">
                    @forelse ($tkItems as $jawaban)
                        <li>{{ $jawaban->tindakanKoreksi?->analisis_ketidaksesuaian ?: 'Belum ada analisa ketidaksesuaian.' }}</li>
                    @empty
                        <li class="empty-text">Belum ada analisa ketidaksesuaian.</li>
                    @endforelse
                </ol>
            </td>
            <td class="sign-cell">
                Auditor<br>
                Tgl : {{ $tanggalRumusan }}
                <div class="sign-space"></div>
                ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Diketahui Ka. AMI / Ka. P4MP<br>
                Tgl : {{ $tanggalRumusan }}
                <div class="sign-space"></div>
                ( {{ $namaP4mp }} )
            </td>
        </tr>
    </table>

    <table class="section-table section-medium">
        <tr>
            <th colspan="2">Usulan Tindakan Koreksi :</th>
        </tr>
        <tr>
            <td rowspan="2" class="content-cell">
                <ol class="section-list">
                    @forelse ($tkItems as $jawaban)
                        <li>{{ $jawaban->tindakanKoreksi?->rencana_koreksi ?: 'Belum ada usulan tindakan koreksi.' }}</li>
                    @empty
                        <li class="empty-text">Belum ada usulan tindakan koreksi.</li>
                    @endforelse
                </ol>
            </td>
            <td class="sign-cell">
                Auditor<br>
                Tgl : {{ $tanggalRumusan }}
                <div class="sign-space"></div>
                ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Diketahui Ka. Unit Kerja<br>
                Tgl : {{ $tanggalRumusan }}
                <div class="sign-space"></div>
                ( {{ $namaAuditee }} )
            </td>
        </tr>
    </table>
</div>

<div class="sheet page-break">
    <table class="section-table section-large" style="margin-top: 28px;">
        <tr>
            <th colspan="2">Pelaksanaan Tindakan Koreksi :</th>
        </tr>
        <tr>
            <td rowspan="2" class="content-cell">
                <ol class="section-list">
                    @forelse ($tkItems as $jawaban)
                        @php
                            $tk = $jawaban->tindakanKoreksi;
                        @endphp
                        <li>{{ $tk?->hasil_penilaian_auditor ?: 'Belum ada hasil penilaian ulang auditor.' }}</li>
                    @empty
                        <li class="empty-text">Belum ada hasil penilaian ulang auditor.</li>
                    @endforelse
                </ol>
            </td>
            <td class="sign-cell">
                Auditor<br>
                Tgl : {{ $tanggalPelaksanaan }}
                <div class="sign-space"></div>
                ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Disetujui Ka. Unit Kerja<br>
                Tgl : {{ $tanggalPelaksanaan }}
                <div class="sign-space"></div>
                ( {{ $namaAuditee }} )
            </td>
        </tr>
    </table>

    <table class="section-table section-large" style="margin-top: 18px;">
        <tr>
            <th colspan="2">Verifikasi Pelaksanaan Tindakan Koreksi :</th>
        </tr>
        <tr>
            <td rowspan="2" class="content-cell">
                @if ($catatanUmumVerifikasi)
                    <p class="verification-note">{!! nl2br(e($catatanUmumVerifikasi)) !!}</p>
                @elseif ($catatanItemVerifikasi->isEmpty())
                    <p class="verification-note">
                        {{ $tkItems->first()?->tindakanKoreksi?->hasil_penilaian_auditor ?: 'Belum ada verifikasi pelaksanaan tindakan koreksi.' }}
                    </p>
                @elseif ($catatanItemVerifikasi->isNotEmpty())
                    <ol class="verification-detail">
                        @foreach ($catatanItemVerifikasi as $catatan)
                            <li>{{ $catatan }}</li>
                        @endforeach
                    </ol>
                @endif
            </td>
            <td class="sign-cell">
                Kepala P4MP<br>
                Tgl : {{ $tanggalP4mp }}
                <div class="sign-space"></div>
                ( {{ $namaP4mp }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Wadir I<br>
                Tgl : {{ $tanggalP4mp }}
                <div class="sign-space"></div>
                ( {{ $namaWadir }} )
            </td>
        </tr>
    </table>

    <div class="notes">
        <p>Catatan : a. Formulir ini berlaku untuk semua jenis ketidaksesuaian (KTS)</p>
        <p style="margin-left: 55px;">b. Formulir ini diisi sesuai dengan temuan</p>
        <p style="margin-left: 55px;">c. Bila diperlukan lampirkan dokumen pendukung</p>
    </div>
</div>
</body>
</html>
