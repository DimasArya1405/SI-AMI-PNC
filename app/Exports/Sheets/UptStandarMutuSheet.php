<?php

namespace App\Exports\Sheets;

use App\Models\Upt;
use App\Models\Periode;
use App\Models\StandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UptStandarMutuSheet implements FromView, WithTitle, WithEvents, WithColumnWidths, WithDrawings
{
    protected $upt_id;
    protected $periode_id;
    protected $standar_mutu_id;

    public function __construct($upt_id, $periode_id, $standar_mutu_id)
    {
        $this->upt_id = $upt_id;
        $this->periode_id = $periode_id;
        $this->standar_mutu_id = $standar_mutu_id;
    }

    public function view(): View
    {
        $upt = Upt::findOrFail($this->upt_id);
        $periode = Periode::findOrFail($this->periode_id);
        $standar = StandarMutu::findOrFail($this->standar_mutu_id);

        $subStandar = UptSubStandarMutu::with(['items'])
            ->where('upt_id', $this->upt_id)
            ->where('periode_id', $this->periode_id)
            ->where('standar_mutu_id', $this->standar_mutu_id)
            ->orderBy('urutan')
            ->get();

        return view('admin.export.upt_standar_mutu', compact(
            'upt',
            'periode',
            'standar',
            'subStandar'
        ));
    }

    public function title(): string
    {
        $nama = StandarMutu::where('standar_mutu_id', $this->standar_mutu_id)
            ->value('nama_standar_mutu') ?? 'STANDAR';

        return substr(strtoupper(str_replace(['\\', '/', '?', '*', '[', ']'], '', $nama)), 0, 31);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 68,
            'C' => 10,
            'D' => 10,
            'E' => 55,
            'F' => 16,
        ];
    }

    public function drawings()
    {
        $drawings = [];

        if (file_exists(public_path('images/logo-pnc.png'))) {
            $logoLeft = new Drawing();
            $logoLeft->setName('Logo PNC');
            $logoLeft->setPath(public_path('images/logo-pnc.png'));
            $logoLeft->setHeight(70);
            $logoLeft->setCoordinates('A1');
            $logoLeft->setOffsetX(12);
            $logoLeft->setOffsetY(5);
            $drawings[] = $logoLeft;
        }

        if (file_exists(public_path('images/logo-ami.png'))) {
            $logoRight = new Drawing();
            $logoRight->setName('Logo AMI');
            $logoRight->setPath(public_path('images/logo-ami.png'));
            $logoRight->setHeight(70);
            $logoRight->setCoordinates('F1');
            $logoRight->setOffsetX(12);
            $logoRight->setOffsetY(5);
            $drawings[] = $logoRight;
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('B1:E1');
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('F1:F2');
                $sheet->mergeCells('B2:C2');
                $sheet->mergeCells('E2:F2');

                $sheet->getRowDimension(1)->setRowHeight(55);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(18);
                $sheet->getRowDimension(4)->setRowHeight(22);
                $sheet->getRowDimension(5)->setRowHeight(22);
                $sheet->getRowDimension(6)->setRowHeight(22);
                $sheet->getRowDimension(7)->setRowHeight(22);
                $sheet->getRowDimension(8)->setRowHeight(18);

                $sheet->getStyle('A1:F' . $highestRow)->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'size' => 10,
                        'color' => ['rgb' => '000000'],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getStyle('A1:F2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('B1:E1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'],
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('B2:C2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '9DC3E6'],
                    ],
                    'font' => [
                        'bold' => true,
                        'italic' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('D2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('E2:F2')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '9DC3E6'],
                    ],
                    'font' => [
                        'bold' => true,
                        'italic' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A4:F7')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00'],
                    ],
                    'font' => ['bold' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->mergeCells('C4:E4');
                $sheet->mergeCells('F4:F7');
                $sheet->mergeCells('C5:E5');
                $sheet->mergeCells('C6:E6');
                $sheet->mergeCells('C7:E7');

                $sheet->getStyle('A4:F7')->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle('F4:F7')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_TOP);

                for ($row = 1; $row <= $highestRow; $row++) {
                    $valueA = strtoupper(trim((string) $sheet->getCell('A' . $row)->getValue()));

                    if (str_starts_with($valueA, 'STANDAR') && $valueA !== 'STANDAR MUTU') {
                        $sheet->mergeCells("A{$row}:F{$row}");
                        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 13,
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_LEFT,
                            ],
                        ]);

                        $sheet->getRowDimension($row)->setRowHeight(24);
                    }

                    if ($valueA === 'NO') {
                        $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFFF00'],
                            ],
                            'font' => ['bold' => true],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ]);

                        $sheet->getRowDimension($row)->setRowHeight(25);
                    }
                }

                $sheet->getStyle('A9:F' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F:F')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                for ($row = 10; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(34);
                }

                $this->mergeAuditeeColumn($sheet, $highestRow);
            },
        ];
    }

    private function mergeAuditeeColumn(Worksheet $sheet, int $highestRow): void
    {
        $start = null;

        for ($row = 1; $row <= $highestRow; $row++) {
            $valueA = strtoupper(trim((string) $sheet->getCell('A' . $row)->getValue()));

            if ($valueA === 'NO') {
                if ($start !== null && ($row - 2) >= $start) {
                    $sheet->mergeCells("F{$start}:F" . ($row - 2));
                }

                $start = $row + 1;
            }
        }

        if ($start !== null && $highestRow >= $start) {
            $sheet->mergeCells("F{$start}:F{$highestRow}");
        }
    }
}