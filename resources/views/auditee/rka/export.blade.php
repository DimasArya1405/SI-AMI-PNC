<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ringkasan Kondisi Audit</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
            text-align: center;
        }

        h2 {
            font-size: 14px;
            margin: 18px 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .meta td:first-child {
            width: 28%;
            font-weight: bold;
            background: #f9fafb;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Ringkasan Kondisi Audit</h1>
    <p class="center">{{ $upt->nama_upt ?? '-' }} - Periode {{ $periode->tahun ?? '-' }}</p>

    <h2>Informasi RKA</h2>
    <table class="meta">
        <tr>
            <td>Tanggal Audit</td>
            <td>{{ $penugasan->tanggal_audit ? \Illuminate\Support\Carbon::parse($penugasan->tanggal_audit)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Tim Auditor</td>
            <td>
                {{ $penugasan->auditor1?->nama_lengkap ?? '-' }}
                @if ($penugasan->auditor2)
                    , {{ $penugasan->auditor2?->nama_lengkap }}
                @endif
            </td>
        </tr>
        <tr>
            <td>Tanggal Rapat Internal</td>
            <td>{{ optional($rka->tanggal_rapat)->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td>Finalisasi</td>
            <td>{{ optional($rka->finalized_at)->translatedFormat('d F Y H:i') ?? '-' }}</td>
        </tr>
    </table>

    <h2>Rumusan Final Kondisi Audit</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 22%;">Standar</th>
                <th style="width: 28%;">Item Pertanyaan</th>
                <th>Kondisi Final</th>
                <th style="width: 10%;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rka->temuan as $index => $temuan)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $temuan->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->nama_standar_mutu ?? '-' }}</td>
                    <td>{{ $temuan->jawabanAudit?->itemSubStandar?->nama_item ?? '-' }}</td>
                    <td>{{ $temuan->kondisi_final }}</td>
                    <td class="center">{{ $temuan->kategori_final }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center">Tidak ada temuan pada RKA final.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
