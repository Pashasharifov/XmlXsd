<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class XlsxParserService
{
    public function parse(string $filePath): array
    {
        if (!file_exists($filePath))
            throw new \Exception("File not found: {$filePath}");
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = [];
        foreach ($sheet->toArray(null, true, true, true) as $index => $row) {
            $rows[] = $row;
            Log::info("Parsed row #{$index}: " . json_encode($row, JSON_UNESCAPED_UNICODE));
        }
        return $rows;
    }
}
