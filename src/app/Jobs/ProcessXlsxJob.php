<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Services\XlsxParserService;
use App\Services\XmlBuilderService;
use App\Services\XsdValidatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessXlsxJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected Upload $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    public function handle(
        XlsxParserService $xlsxParser,
        XmlBuilderService $xmlBuilder,
        XsdValidatorService $xsdValidator
    ): void {
        $this->upload->update(['status' => 'processing']);

        try {
            Log::info("Processing XLSX upload ID={$this->upload->id}");

            $filePath = storage_path("app/private/{$this->upload->filepath}");
            Log::info("XLSX file path: {$filePath}");
            if (!file_exists($filePath))
                throw new \Exception("File not found: {$filePath}");

            $rows = $xlsxParser->parse($filePath);
            Log::info("Parsed " . count($rows) . " rows from XLSX.");

            $xmlContent = $xmlBuilder->build($rows);
            Log::info("XML are built from XLSX data.");

            // $xmlPath = storage_path("app/private/xml/{$xmlTempPath}.xml");
            // file_put_contents($xmlPath, $xmlContent);

            $xsdPath = resource_path('IPD4upload.xsd');
            $isValid = $xsdValidator->validate($xmlContent, $xsdPath);

            if (!$isValid)
                throw new \Exception('XML is not like XSD.');

            $xmlTempPath = 'processed/' . pathinfo($this->upload->filename, PATHINFO_FILENAME) . '.xml';
            Storage::put($xmlTempPath, $xmlContent);
            Log::info("Validated XML saved to: {$xmlTempPath}");

            $this->upload->update([
                'status' => 'success',
                'error_message' => null,
            ]);

            Log::info("Upload ID={$this->upload->id} successfully processed.");

        } catch (\Throwable $e) {
            Log::error("Error processing XLSX upload ID={$this->upload->id}: {$e->getMessage()}");

            $this->upload->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
