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
            page-break-inside: avoid;
            break-inside: avoid;
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
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .content-cell {
            width: 75%;
        }

        .sign-cell {
            font-size: 10.5pt;
            height: auto;
            width: 25%;
        }

        .section-large .content-cell {
            height: 125px;
        }

        .section-medium .content-cell {
            height: 95px;
        }

        .section-small .content-cell {
            height: 125px;
        }

        .sign-space {
            height: 10px;
        }

        .qr-code {
            display: block;
            height: 56px;
            margin: 3px auto 2px;
            object-fit: contain;
            width: 56px;
        }

        .sign-block {
            min-height: 74px;
        }

        .sign-divider {
            border-top: 1.25px solid #444;
            margin: 5px -6px 5px;
        }

        .section-list {
            margin: 0;
            padding-left: 18px;
        }

        .finding-list {
            min-height: 126px;
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
            margin: 0 -6px -6px;
            padding: 7px 6px;
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
    \Illuminate\Support\Carbon::setLocale('id');
    $formatTanggal = function ($date, bool $withTime = false) {
        if (!$date) {
            return '-';
        }

        return \Illuminate\Support\Carbon::parse($date)
            ->locale('id')
            ->translatedFormat($withTime ? 'j F Y H:i' : 'j F Y');
    };
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
    $tanggalAudit = $formatTanggal($penugasan->tanggal_audit);
    $tkItems = $temuan->filter(fn ($jawaban) => $jawaban->tindakanKoreksi)->values();
    $tkPertama = $penugasan->tindakanKoreksi?->first();
    $verifikasiTk = $penugasan->verifikasiTindakanKoreksi;
    $auditeeSigned = $tkItems->isNotEmpty()
        && $tkItems->every(fn ($jawaban) => filled($jawaban->tindakanKoreksi?->auditee_signed_at));
    $catatanUmumVerifikasi = trim((string) $verifikasiTk?->catatan_umum);
    $catatanUmumVerifikasi = preg_replace('/^\s*(\d+\.\s*)?Temuan\s+\d+\s+-\s*[^:]+:\s*/mi', '$1', $catatanUmumVerifikasi);
    $catatanItemVerifikasi = $tkItems
        ->map(fn ($jawaban) => trim((string) $jawaban->tindakanKoreksi?->p4mp_catatan))
        ->filter()
        ->values();
    $tanggalRka = $formatTanggal($temuan->first()?->rkaTemuan?->rka?->tanggal_rapat);
    $tanggalRka = $tanggalRka !== '-' ? $tanggalRka : $tanggalAudit;
    $tanggalRumusan = $formatTanggal($tkItems->first()?->tindakanKoreksi?->created_at);
    $tanggalRumusan = $tanggalRumusan !== '-' ? $tanggalRumusan : $tanggalAudit;
    $tanggalPelaksanaan = $formatTanggal($tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->tanggal_penilaian_ulang)?->tindakanKoreksi?->tanggal_penilaian_ulang);
    $tanggalAuditee = $formatTanggal($tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->auditee_signed_at)?->tindakanKoreksi?->auditee_signed_at);
    $tanggalP4mp = $verifikasiTk?->finalized_at
        ? $formatTanggal($verifikasiTk->finalized_at)
        : $formatTanggal($tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->p4mp_verified_at)?->tindakanKoreksi?->p4mp_verified_at);
    $namaP4mp = $verifikasiTk?->finalizedBy?->name
        ?? $tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->p4mpVerifiedBy)?->tindakanKoreksi?->p4mpVerifiedBy?->name
        ?? 'Kepala P4MP';
    $namaWadir = trim((string) (
        $verifikasiTk?->wadir1_nama
        ?? $tkItems->first(fn ($jawaban) => $jawaban->tindakanKoreksi?->wadir1_nama)?->tindakanKoreksi?->wadir1_nama
        ?? ''
    ));
    $wadirDitandatangani = $namaWadir !== '' && !empty($wadirQR);
    $placeholderWadir = '...................................';
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

    <div class="title">FORMULIR USULAN TINDAKAN KOREKSI</div>

    <table class="section-table section-large">
        <tr>
            <th colspan="2">Temuan/Laporan Ketidaksesuaian :</th>
        </tr>
        <tr>
            <td rowspan="2" class="content-cell">
                <ol class="section-list finding-list">
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
                  @if ($tkPertama?->verified_by_user_id && !empty($ketuaQR))
                    <img src="{{ $ketuaQR }}" alt="QR" class="qr-code">
                    @else
                    <i style="color:red;">Belum disetujui.</i>
                    @endif
                <div class="sign-space">
                    
                </div>
                ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Auditor<br>
                Tgl : {{ $tanggalRka }}
                  @if ($tkPertama?->verified_by_user_id && !empty($anggotaQR))
                    <img src="{{ $anggotaQR }}" alt="QR" class="qr-code">
                    @else
                    <i style="color:red;">Belum disetujui.</i>
                    @endif
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
                  @if ($tkPertama?->verified_by_user_id && !empty($ketuaQR))
                    <img src="{{ $ketuaQR }}" alt="QR" class="qr-code">
                    @else
                    <i style="color:red;">Belum disetujui.</i>
                    @endif
                <div class="sign-space"></div>
                ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Diketahui Ka. AMI / Ka. P4MP<br>
                Tgl : {{ $tanggalP4mp }}
                  @if ($tkPertama?->p4mp_verified_by_user_id && !empty($kepalaQR))
                    <img src="{{ $kepalaQR }}" alt="QR" class="qr-code">
                    @else
                    <i style="color:red;">Belum disetujui.</i>
                    @endif
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
            <td class="content-cell">
                <ol class="section-list">
                    @forelse ($tkItems as $jawaban)
                        <li>{{ $jawaban->tindakanKoreksi?->rencana_koreksi ?: 'Belum ada usulan tindakan koreksi.' }}</li>
                    @empty
                        <li class="empty-text">Belum ada usulan tindakan koreksi.</li>
                    @endforelse
                </ol>
            </td>
            <td class="sign-cell">
                <div class="sign-block">
                    Auditor<br>
                    Tgl : {{ $tanggalRumusan }}
                    @if ($tkPertama?->verified_by_user_id && !empty($ketuaQR))
                        <img src="{{ $ketuaQR }}" alt="QR" class="qr-code">
                    @else
                        <i style="color:red;">Belum disetujui.</i>
                    @endif
                    <div class="sign-space"></div>
                    ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
                </div>
                <div class="sign-divider"></div>
                <div class="sign-block">
                    Diketahui Ka. Unit Kerja<br>
                    Tgl : {{ $tanggalAuditee }}
                    @if ($auditeeSigned && !empty($auditeeQR))
                        <img src="{{ $auditeeQR }}" alt="QR" class="qr-code">
                    @else
                        <i style="color:red;">Belum disetujui.</i>
                    @endif
                    <div class="sign-space"></div>
                    ( {{ $namaAuditee }} )
                </div>
            </td>
        </tr>
    </table>

    <table class="section-table section-large" style="margin-top: 12px;">
        <tr>
            <th colspan="2">Pelaksanaan Tindakan Koreksi :</th>
        </tr>
        <tr>
            <td class="content-cell">
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
                <div class="sign-block">
                    Auditor<br>
                    Tgl : {{ $tanggalPelaksanaan }}
                    @if ($tkPertama?->verified_by_user_id && !empty($ketuaQR))
                        <img src="{{ $ketuaQR }}" alt="QR" class="qr-code">
                    @else
                        <i style="color:red;">Belum disetujui.</i>
                    @endif
                    <div class="sign-space"></div>
                    ( {{ $penugasan->auditor1?->nama_lengkap ?? '-' }} )
                </div>
                <div class="sign-divider"></div>
                <div class="sign-block">
                    Disetujui Ka. Unit Kerja<br>
                    Tgl : {{ $tanggalAuditee }}
                    @if ($auditeeSigned && !empty($auditeeQR))
                        <img src="{{ $auditeeQR }}" alt="QR" class="qr-code">
                    @else
                        <i style="color:red;">Belum disetujui.</i>
                    @endif
                    <div class="sign-space"></div>
                    ( {{ $namaAuditee }} )
                </div>
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
                @elseif ($catatanItemVerifikasi->isNotEmpty())
                    <ol class="verification-detail">
                        @foreach ($catatanItemVerifikasi as $catatan)
                            <li>{{ $catatan }}</li>
                        @endforeach
                    </ol>
                @endif
            </td>
                <td class="sign-cell">
                Diketahui Ka. AMI / Ka. P4MP<br>
                Tgl : {{ $tanggalP4mp }}
                  @if ($tkPertama?->p4mp_verified_by_user_id && !empty($kepalaQR))
                    <img src="{{ $kepalaQR }}" alt="QR" class="qr-code">
                    @else
                    <i style="color:red;">Belum disetujui.</i>
                    @endif
                <div class="sign-space"></div>
                ( {{ $namaP4mp }} )
            </td>
        </tr>
        <tr>
            <td class="sign-cell">
                Wadir I<br>
                Tgl : {{ $tanggalP4mp }}
                @if ($wadirDitandatangani)
                    <img src="{{ $wadirQR }}" alt="QR" class="qr-code">
                @endif
                <div class="sign-space"></div>
                ( {{ $namaWadir !== '' ? $namaWadir : $placeholderWadir }} )
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
