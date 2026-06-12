<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen AMI - Dummy Preview</title>

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

        h2, h3 {
            margin: 10px 0;
        }

        .judul {
            font-weight: bold;
            font-size: 20px;
            margin: 20px auto;
            line-height: 1.5;
            max-width: 850px;
            color: #1a56db; /* Memberi aksen pembeda untuk data utama */
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

        /* Alert Box tanda dummy aktif agar tidak membingungkan saat dev */
        .dummy-badge {
            background-color: #fef3c7;
            color: #92400e;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            display: inline-block;
            font-family: sans-serif;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <br>

        {{-- Logo Placeholder (Menggunakan layanan generator gambar agar tidak broken) --}}
        <img src="{{ asset('img/logo-pnc-1.png') }}" style="width: 100px;" alt="Logo Instansi">

        {{-- Header --}}
        <h3>Pusat Penjaminan Mutu menyatakan bahwa dokumen {{$judul}} tahun {{$tahun}}</b></h3>
        <p class="keterangan">dengan lingkup:</p>

        {{-- Judul / Lingkup Audit --}}
        <div class="judul">
            @if($penugasan->upt->kategori_upt == 'Prodi')
            Prodi :
            @else
            Unit/Bagian :
            @endif
            {{ $penugasan->upt->nama_upt }}
        </div>

        {{-- Pernyataan tanda tangan --}}
        <p class="keterangan">
            adalah sah dan secara resmi ditandatangani secara digital oleh:
        </p>

        <div class="penandatangan">
            {{$nama}} <br>
            <span style="font-weight: normal;">{{$jabatan}}</span>
        </div>

        {{-- Footer --}}
        <div class="keterangan-bottom">
            Sistem Informasi Audit Mutu Internal (AMI), Pusat Pengembangan Pembelajaran dan Penjaminan Mutu Pendidikan Politeknik Negeri Cilacap
        </div>
        <footer class="footer">
            <div class="footer-small">
                © 2026 Kantor P4MP
            </div>
        </footer>
    </div>
</body>
</html>