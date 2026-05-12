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
            <td style="width: 20%;">Kriteria</td>
            <td>: Standar SPMI</td>
        </tr>
        <tr>
            <td style="width: 20%;">Tgl Penilaian</td>
            <td>: </td>
        </tr>
        <tr>
            <td style="width: 20%;">Auditi</td>
            <td>: </td>
        </tr>
        <tr>
            <td style="width: 20%;">Auditor</td>
            <td>: </td>
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
{{$s->standar_mutu->nama_standar}}
    @php
        $temuan = [];

        foreach ($s->uptSubStandar as $sub) {
            foreach ($sub->items as $item) {

                foreach ($item->jawaban_audit as $jawaban) {
                    dd($jawaban);
                    if ($jawaban->jawaban == false || $jawaban->jawaban == 0) {
                        $temuan[] = [
                            'catatan' => $jawaban->catatan,
                            'kategori' => 'KTS'
                        ];
                        
                    }

                }
            }
        }

        $jumlahTemuan = count($temuan);
    @endphp
@dd($jumlahTemuan)
    @if($jumlahTemuan > 0)

        @foreach($temuan as $index => $t)
            <tr>

                @if($index == 0)
                    <td rowspan="{{ $jumlahTemuan }}">
                        {{ $s->standar_mutu->nama_standar }}
                    </td>
                @endif

                <td>
                    {{ $t['catatan'] }}
                </td>

                <td class="center">
                    {{ $t['kategori'] }}
                </td>

            </tr>
        @endforeach

    @else

        <tr>
            <td>
                {{ $s->standar_mutu->nama_standar_mutu }}
            </td>

            <td></td>

            <td></td>
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