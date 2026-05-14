<?php

namespace App\Domains\Common\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportToExcelService
{
    /**
     * Export an Eloquent collection to Excel.
     *
     * @param Collection<int, \Illuminate\Database\Eloquent\Model> $items
     * @param array<string>|null $fields Optional fields to export
     * @param string $sheetName
     * @param string $fileName
     *
     * @return StreamedResponse
     */
    public static function download(
        Collection $items,
        ?array $fields = null,
        string $sheetName = 'Sheet1',
        string $fileName = 'export.xlsx',
    ): StreamedResponse {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetName);

        if ($items->isEmpty()) {
            $sheet->setCellValue('A1', 'No data found');

            return self::streamDownload($spreadsheet, $fileName);
        }

        /**
         * Determine fields automatically
         */
        $firstItem = $items->first();

        $fields ??= array_keys(
            $firstItem->getAttributes()
        );

        /**
         * Write headers
         */
        foreach ($fields as $columnIndex => $field) {
            $column = $columnIndex + 1;

            $cell = Coordinate::stringFromColumnIndex($column) . '1';

            $sheet->setCellValue($cell, Str::headline($field));
        }

        /**
         * Write rows
         */
        foreach ($items as $rowIndex => $item) {
            foreach ($fields as $columnIndex => $field) {
                $column = $columnIndex + 1;
                $row = $rowIndex + 2;

                $value = data_get($item, $field);

                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_THROW_ON_ERROR);
                }

                $cell = Coordinate::stringFromColumnIndex($column) . $row;
                $sheet->setCellValue($cell, $value);
            }
        }

        /**
         * Auto-size columns
         */
        foreach (range(1, count($fields)) as $columnIndex) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                $columnIndex
            );

            $sheet
                ->getColumnDimension($columnLetter)
                ->setAutoSize(true);
        }

        return self::streamDownload($spreadsheet, $fileName);
    }

    protected static function streamDownload(
        Spreadsheet $spreadsheet,
        string $fileName,
    ): StreamedResponse {
        return response()->streamDownload(
            function () use ($spreadsheet): void {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            $fileName,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
