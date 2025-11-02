<?php

namespace App\Services;

use DOMDocument;
use Illuminate\Support\Facades\Log;

class XsdValidatorService
{
    public function validate(string $xmlContent, string $xsdPath): bool
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadXML($xmlContent);

        $isValid = $dom->schemaValidate($xsdPath);

        if (!$isValid) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                Log::error("XSD Validation Error: " . trim($error->message));
            }
            libxml_clear_errors();
        }
        else {
            Log::info("XML is valid against the XSD schema.");
        }

        return $isValid;
    }
}
