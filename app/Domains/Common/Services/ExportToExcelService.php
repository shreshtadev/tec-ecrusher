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
        ?bool $isForTripSheet = false,
    ): StreamedResponse {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetName);

        if ($items->isEmpty()) {
            $sheet->setCellValue('A1', 'No data found');

            return self::streamDownload($spreadsheet, $fileName);
        }

        $firstItem = $items->first();

        $fields ??= array_keys(
            $firstItem->getAttributes()
        );

        /*
|--------------------------------------------------------------------------
| Support both:
|--------------------------------------------------------------------------
|
| [
|     'challan_number',
|     'quantity_cft',
| ]
|
| and
|
| [
|     'challan_number' => 'Challan No',
|     'party.full_name' => 'Party',
| ]
|
*/

        $isAssociative = array_keys($fields) !== range(
            0,
            count($fields) - 1
        );

        $fieldKeys = $isAssociative
            ? array_keys($fields)
            : $fields;

        $headers = $isAssociative
            ? array_values($fields)
            : array_map(
                fn(string $field) => Str::headline($field),
                $fields
            );

        /*
|--------------------------------------------------------------------------
| Headers
|--------------------------------------------------------------------------
*/

        foreach ($headers as $columnIndex => $header) {

            $column = $columnIndex + 1;

            $cell = Coordinate::stringFromColumnIndex($column) . '1';

            $sheet->setCellValue($cell, $header);
        }

        /*
|--------------------------------------------------------------------------
| Rows
|--------------------------------------------------------------------------
*/

        foreach ($items as $rowIndex => $item) {

            foreach ($fieldKeys as $columnIndex => $field) {

                $column = $columnIndex + 1;
                $row = $rowIndex + 2;

                $value = data_get($item, $field);

                if ($value instanceof \BackedEnum) {
                    $value = $value->value;
                }

                if ($value instanceof \DateTimeInterface) {
                    if ($isForTripSheet) {
                        $value = $value->format('H:i');
                    } else {
                        $value = $value->format('Y-m-d H:i:s');
                    }
                }

                if (is_array($value)) {
                    $value = json_encode(
                        $value,
                        JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                    );
                }

                $cell = Coordinate::stringFromColumnIndex($column) . $row;

                $sheet->setCellValue($cell, $value);
            }
        }

        /*
|--------------------------------------------------------------------------
| Auto Size
|--------------------------------------------------------------------------
*/

        foreach (range(1, count($headers)) as $columnIndex) {

            $columnLetter = Coordinate::stringFromColumnIndex(
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
