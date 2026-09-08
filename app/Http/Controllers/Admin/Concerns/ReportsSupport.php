<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Http\Controllers\Admin\DashboardController;
use App\Models\OutreachSite;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Shared building blocks for the admin Reports hub.
 *
 * Geography lookups come from the `outreach_sites` catalogue (the single source
 * of District -> Union Council -> Fix Site), and the Excel helpers write every
 * cell as a string so codes (CNIC, phone) keep their formatting. Reused by every
 * report controller so the workbooks and pickers look and behave the same.
 */
trait ReportsSupport
{
    /** Distinct Union Councils from the outreach-site catalogue. */
    protected function unionCouncils()
    {
        return OutreachSite::query()
            ->whereNotNull('union_council')
            ->where('union_council', '!=', '')
            ->pluck('union_council')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /** Distinct districts from the outreach-site catalogue. */
    protected function districts()
    {
        return OutreachSite::query()
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->pluck('district')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /** Distinct fixed sites belonging to a Union Council. */
    protected function fixSitesForUc(string $uc)
    {
        return OutreachSite::query()
            ->where('union_council', $uc)
            ->whereNotNull('fix_site')
            ->where('fix_site', '!=', '')
            ->pluck('fix_site')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }

    /**
     * Resolve the spelling variants a UC may appear under in the form tables.
     * Form data uses many inconsistent spellings; DashboardController keeps the
     * canonical consolidation map, so we reuse it here.
     */
    protected function ucVariants(string $uc): array
    {
        $consolidated = DashboardController::getConsolidatedUcName($uc);
        $variants = DashboardController::getUcVariants($consolidated);

        return array_values(array_unique(array_filter(
            array_merge([$uc, $consolidated], $variants)
        )));
    }

    /** A fresh workbook with standard document properties. */
    protected function newReportSpreadsheet(string $title, string $subject = ''): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle($title)
            ->setSubject($subject)
            ->setCreator(auth()->user()->name ?? 'Admin');

        return $spreadsheet;
    }

    /**
     * Write a single worksheet from a header row and an array of data rows.
     * All cells are written as strings so codes (CNIC, phone) keep their format.
     */
    protected function writeSheet(Spreadsheet $spreadsheet, int $index, string $title, array $headers, array $rows, string $emptyMessage = 'No records.'): void
    {
        $sheet = $index === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
        $sheet->setTitle(Str::limit($title, 31, ''));

        $colCount = count($headers);

        foreach ($headers as $i => $header) {
            $sheet->setCellValueExplicit(
                Coordinate::stringFromColumnIndex($i + 1) . '1',
                $header,
                DataType::TYPE_STRING
            );
        }

        $rowNum = 2;
        foreach ($rows as $row) {
            $col = 1;
            foreach ($row as $value) {
                $sheet->setCellValueExplicit(
                    Coordinate::stringFromColumnIndex($col) . $rowNum,
                    $value === null ? '' : (string) $value,
                    DataType::TYPE_STRING
                );
                $col++;
            }
            $rowNum++;
        }

        if ($colCount > 0) {
            $lastCol = Coordinate::stringFromColumnIndex($colCount);
            $headerStyle = $sheet->getStyle('A1:' . $lastCol . '1');
            $headerStyle->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('047857');
            $sheet->freezePane('A2');
            $sheet->setAutoFilter('A1:' . $lastCol . '1');

            for ($c = 1; $c <= $colCount; $c++) {
                $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
            }
        }

        if (empty($rows) && $emptyMessage !== '') {
            $sheet->setCellValue('A2', $emptyMessage);
        }

        $sheet->setSelectedCell('A1');
    }

    /** Stream a workbook to the browser as an .xlsx download. */
    protected function streamWorkbook(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
