<table>
    <tr>
        <td></td>
        <td colspan="4">FORMULIR AUDIT MUTU INTERNAL</td>
        <td></td>
    </tr>

    <tr>
        <td></td>
        <td colspan="2">POLITEKNIK NEGERI CILACAP</td>
        <td>{{ $periode->tahun }}</td>
        <td colspan="2">FM.SOP/AMI-Q.02-01</td>
    </tr>

    <tr></tr>

    <tr>
        <td></td>
        <td>Kriteria Standar</td>
        <td colspan="3">{{ strtoupper($standar->nama_standar_mutu) }}</td>
        <td>Tgl Penilaian</td>
    </tr>

    <tr>
        <td></td>
        <td>Program Studi</td>
        <td colspan="3">{{ $upt->nama_upt }}</td>
        <td></td>
    </tr>

    <tr>
        <td></td>
        <td>Auditor</td>
        <td colspan="3">1.</td>
        <td></td>
    </tr>

    <tr>
        <td></td>
        <td></td>
        <td colspan="3">2.</td>
        <td></td>
    </tr>

    <tr></tr>

    @foreach ($subStandar as $sub)
        <tr>
            <td colspan="6">{{ strtoupper($sub->nama_sub_standar) }}</td>
        </tr>

        <tr>
            <td>NO</td>
            <td>PERTANYAAN DAN PERNYATAAN</td>
            <td>YA</td>
            <td>TIDAK</td>
            <td>URAIAN (BUKTI)</td>
            <td>AUDITEE</td>
        </tr>

        @foreach ($sub->items->whereNull('parent_upt_item_id') as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_item }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ strtoupper($upt->nama_upt) }}</td>
            </tr>

            @foreach ($sub->items->where('parent_upt_item_id', $item->upt_item_sub_standar_id) as $child)
                <tr>
                    <td></td>
                    <td>- {{ $child->nama_item }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ strtoupper($upt->nama_upt) }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr></tr>
    @endforeach
</table>