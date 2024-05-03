<?php

namespace App\Exporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Exporter des données liées à une société.
 */
class LessorExporter
{
    final public const FORMAT = [
        'xlsx' => [
            'format' => 'xlsx',
            'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'csv' => ['format' => 'csv', 'mimeType' => 'text/csv'],
    ];

    final public const EXTRACT_HEADER = [
        'A' => 'Nom',
        'B' => 'Prénom',
        'C' => 'Email',
        'D' => 'Adresse',
        'E' => 'Téléphone',
        'F' => 'Compte stripe',
        'G' => 'Compte bailleur',
        'H' => 'Invité',
        'I' => 'Date d’inscription',
        'J' => 'Date dernière mise à jour',
        'K' => 'Date de dernière connexion',
    ];

    /**
     * Génération d'un export des entreprises au format xlsx.
     */
    public function exportAsXLSX(array $customers): array
    {
        return $this->export($customers, 'xlsx');
    }

    /**
     * Génération d'un export des entreprises au format xlsx.
     */
    public function exportAsCSV(array $customers): array
    {
        return $this->export($customers, 'csv');
    }

    /**
     * Export.
     */
    private function export(array $customers, string $fileType): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->initializeHeader($sheet);
        $this->initializeBody($sheet, $customers);

        return [
            'file' => $this->saveFile($spreadsheet, $fileType),
            'contentType' => self::FORMAT[$fileType]['mimeType'],
        ];
    }

    /**
     * Initialisation des entêtes.
     */
    private function initializeHeader(Worksheet $sheet): void
    {
        $x = 1;
        foreach (self::EXTRACT_HEADER as $key => $header) {
            $sheet->setCellValue($key.$x, $header);
        }
    }

    /**
     * Initialisation du corps.
     */
    private function initializeBody(Worksheet $sheet, array $customers): void
    {
        $i = 2;
        foreach ($customers as $customer) {
            $sheet->setCellValue('A'.$i, $customer->getLastname());
            $sheet->setCellValue('B'.$i, $customer->getFirstname());
            $sheet->setCellValue('C'.$i, $customer->getEmail());
            $sheet->setCellValue('D'.$i, $customer->getFullAddress());
            $sheet->setCellValue('E'.$i, $customer->getPhone());
            $sheet->setCellValue('F'.$i, $customer->getStripeCustomerId());
            $sheet->setCellValue('G'.$i, $customer->getStripeAccountId());
            $sheet->setCellValue('H'.$i, ($customer->getIsGuess()) ? 'Oui' : 'Non');
            $sheet->setCellValue('I'.$i, $customer->getCreatedAt()->format('d-m-Y'));
            $sheet->setCellValue('J'.$i, $customer->getUpdatedAt()->format('d-m-Y'));
            $sheet->setCellValue('K'.$i, $customer->getUpdatedAt()->format('d-m-Y'));
            ++$i;
        }
    }

    /**
     * Enregistre le fichier dans un répertoire temporaire.
     */
    private function saveFile(Spreadsheet $spreadsheet, string $fileType = 'csv'): false|string
    {
        if ($fileType === self::FORMAT['xlsx']['format']) {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
        } else {
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(';');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
            $writer->setEnclosureRequired(false);
            $writer->setPreCalculateFormulas(false);
            $writer->setUseBOM(true);
        }

        // Création d'un fichier temporaire
        ob_start();
        $writer->save('php://output');

        return ob_get_clean();
    }
}
