<?php

namespace App\Exporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Exporteur des données liées à une personne.
 */
class UserExporter
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
        'D' => 'Mot de passe',
    ];

    /**
     * Génération d'un export des utilisateurs au format xlsx.
     */
    public function exportAsXLSX(array $users, string $filename = 'user_mailing_list'): void
    {
        $this->export($users, 'xlsx');
    }

    /**
     * Génération d'un export des utilisateurs au format xlsx.
     */
    public function exportAsCSV(array $users, string $filename = 'user_mailing_list'): void
    {
        $this->export($users, 'csv');
    }

    /**
     * Export.
     */
    private function export(array $users, string $fileType): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->initializeHeader($sheet);
        $this->initializeBody($sheet, $users);
        $this->saveFile($spreadsheet, $fileType);
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
    private function initializeBody(Worksheet $sheet, array $users): void
    {
        $i = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A'.$i, $user['billing_last_name'] ?? $user['wpcf-nom-dirigeant']);
            $sheet->setCellValue('B'.$i, $user['billing_first_name'] ?? $user['wpcf-prenom-dirigeant']);
            $sheet->setCellValue('C'.$i, $user['email']);
            $sheet->setCellValue('D'.$i, $user['plainPassword']);
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
