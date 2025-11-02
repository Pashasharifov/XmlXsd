<?php

namespace App\Services;

use DOMDocument;

class XmlBuilderService
{
    public function build(array $rows): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        // Root element
        $root = $dom->createElement('IPD-UPLOAD');
        $dom->appendChild($root);

        // HEADER
        $header = $dom->createElement('HEADER');
        $header->appendChild($dom->createElement('USER-NAME', 'system_user'));
        $header->appendChild($dom->createElement('UPLOADING-SOCIETY', 'GVL'));
        $header->appendChild($dom->createElement('ISO-CHAR-SET', 'ISO8859-1'));
        $root->appendChild($header);

        // RIGHTHOLDERS
        $rightholders = $dom->createElement('RIGHTHOLDERS');
        $root->appendChild($rightholders);

        foreach ($rows as $index => $row) {
            if ($index === 0 || empty($row['B'])) continue;

            $rightholder = $dom->createElement('RIGHTHOLDER');

            $rightholder->appendChild($dom->createElement('ACTION', 'INSERT'));
            $rightholder->appendChild($dom->createElement('RIGHTHOLDER-LOCAL-ID', htmlspecialchars($row['J'] ?? '1')));
            $rightholder->appendChild($dom->createElement('RIGHTHOLDER-FIRST-NAME', htmlspecialchars(trim($row['B'] ?? ''))));
            $rightholder->appendChild($dom->createElement('RIGHTHOLDER-LAST-NAME', htmlspecialchars(trim($row['C'] ?? ''))));
            $rightholder->appendChild($dom->createElement('SEX', htmlspecialchars(trim($row['D'] ?? 'M'))));
            $rightholder->appendChild($dom->createElement('DATE-OF-BIRTH', htmlspecialchars($this->formatDate($row['F'] ?? '1980-01-01'))));
            $rightholder->appendChild($dom->createElement('COUNTRY-OF-RESIDENCE', htmlspecialchars(trim($row['G'] ?? 'AZE'))));

            // Identifying Roles
            $roles = $dom->createElement('IDENTIFYING-ROLES');
            $roleCodes = explode('/', trim($row['L'] ?? 'SI'));
            foreach ($roleCodes as $roleCode) {
                if (trim($roleCode) !== '') {
                    $roles->appendChild($dom->createElement('IDENTIFYING-ROLE-CODE', trim($roleCode)));
                }
            }
            $rightholder->appendChild($roles);

            // Pseudonyms
            if (!empty($row['K'])) {
                $pseudonames = $dom->createElement('PSEUDONAMES');
                $pseudonames->appendChild($dom->createElement('PSEUDONAME', htmlspecialchars(trim($row['K']))));
                $rightholder->appendChild($pseudonames);
            }

            // Mandate Infos
            $mandateInfos = $dom->createElement('MANDATE-INFOS');
            $mandateInfo = $dom->createElement('MANDATE-INFO');
            $mandateInfo->appendChild($dom->createElement('MANDATE-TYPE', htmlspecialchars(trim($row['N'] ?? 'WW'))));
            $mandateInfo->appendChild($dom->createElement('MANDATED-SOCIETY-CODE', htmlspecialchars(trim($row['H'] ?? '100'))));
            $mandateInfo->appendChild($dom->createElement('MANDATED-SOCIETY-NAME', htmlspecialchars(trim($row['I'] ?? 'GVL'))));

            $mandateParams = $dom->createElement('MANDATE-PARAMETERS');
            $mandateParam = $dom->createElement('MANDATE-PARAMETER');
            $mandateParam->appendChild($dom->createElement('TERRITORY-COUNTRY', htmlspecialchars(trim($row['E'] ?? 'AZE'))));
            $mandateParam->appendChild($dom->createElement('MANDATE-START-DATE', htmlspecialchars($this->formatDate($row['O'] ?? '2025-01-01'))));
            $mandateParam->appendChild($dom->createElement('MANDATE-END-DATE', htmlspecialchars($this->formatDate($row['P'] ?? '2026-01-01'))));

            // Rights
            $rights = $dom->createElement('RIGHTS');
            $rightCodes = explode('/', trim($row['R'] ?? 'PP'));
            foreach ($rightCodes as $rightCode) {
                if (trim($rightCode) !== '') {
                    $rights->appendChild($dom->createElement('RIGHT-CODE', trim($rightCode)));
                }
            }
            $mandateParam->appendChild($rights);

            $mandateParams->appendChild($mandateParam);
            $mandateInfo->appendChild($mandateParams);
            $mandateInfos->appendChild($mandateInfo);
            $rightholder->appendChild($mandateInfos);

            $rightholders->appendChild($rightholder);
        }

        return $dom->saveXML();
    }

    private function formatDate(string $date): string
    {
        if (empty($date)) return '';
        try {
            $d = new \DateTime($date);
            return $d->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }
}
