<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen AMI</title>

    <style>
        .wrapper {
            font-family: "Times New Roman", serif;
            text-align: center;
            margin: 30px;
            color: #000;
        }

        img {
            width: 80px;
            margin-bottom: 20px;
        }

        h2,
        h3 {
            margin: 10px 0;
        }

        .judul {
            font-weight: bold;
            font-size: 20px;
            margin: 20px auto;
            line-height: 1.5;
            max-width: 850px;
            color: #1a56db;
        }

        .keterangan {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .penandatangan {
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
        }

        .digital-file {
            margin: 30px auto 10px;
            text-align: center;
        }

        .digital-file a.button {
            display: inline-block;
            padding: 10px 18px;
            background: #1a56db;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            font-size: 14px;
        }

        footer {
            margin-top: 50px;
            font-size: 14px;
            color: #444;
            border-top: 1px solid grey;
        }

        .footer-small {
            text-align: center;
            margin-top: 10px;
            font-size: 12px;
            color: #777;
        }

        .keterangan-bottom {
            margin-top: 40px;
        }

        .lingkup {
            font-size: 18px;
            margin: 18px auto 10px;
            line-height: 1.5;
            max-width: 850px;
            color: #1a56db;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <br>

        <img src="{{ asset('img/logo-pnc-1.png') }}" style="width: 100px;" alt="Logo Instansi">

        <h3>Pusat Penjaminan Mutu menyatakan bahwa dokumen {{ $judul }} tahun {{ $tahun }}</h3>
        <p class="keterangan">dengan lingkup:</p>

        <div class="judul {{ !empty($lingkup) ? 'lingkup' : '' }}">
            @if (!empty($lingkup))
                {{ $lingkup }}
            @else
                @if ($penugasan->upt->kategori_upt == 'Prodi')
                    Prodi :
                @else
                    Unit/Bagian :
                @endif
                {{ $penugasan->upt->nama_upt }}
            @endif
        </div>

        <p class="keterangan">
            adalah sah dan secara resmi ditandatangani secara digital oleh:
        </p>

        <div style="font-size: 18px; margin-top: 20px;">
            Cilacap,
            {{ $tanggalTandaTangan }}
            pukul
            {{ $jamTandaTangan }}{{ $zonaWaktuTandaTangan ? ' ' . $zonaWaktuTandaTangan : '' }}
        </div>

        <div class="penandatangan">
            {{ $nama }} <br>
            <span style="font-weight: normal;">{{ $jabatan }}</span>
        </div>

        <div class="digital-file">
            <a href="{{ $downloadUrl }}" class="button" target="_blank" rel="noopener">Lihat File Digital</a>
        </div>

        <div class="keterangan-bottom">
            Sistem Informasi Audit Mutu Internal (AMI), Pusat Pengembangan Pembelajaran dan Penjaminan Mutu Pendidikan
            Politeknik Negeri Cilacap
        </div>
        <footer class="footer">
            <div class="footer-small">
                © 2026 Kantor P4MP
            </div>
        </footer>
    </div>
</body>

</html>
