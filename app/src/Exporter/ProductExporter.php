<?php

namespace App\Exporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Exporter des données liées à une société.
 */
class ProductExporter
{
    final public const FORMAT = [
        'xlsx' => ['format' => 'xlsx', 'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'csv' => ['format' => 'csv', 'mimeType' => 'text/csv'],
    ];

    final public const EXTRACT_HEADER = [
        'A' => 'Titre',
        'B' => 'Courte description',
        'C' => 'Montant TTC',
        'D' => 'Caution',
        'E' => 'Quantité',
        'F' => 'Quantité réservé',
        'G' => 'Propriétaire',
        'H' => 'Description',
        'I' => 'Date de création',
        'J' => 'Date dernière mise à jour',
    ];

    /**
     * Génération d'un export des entreprises au format xlsx.
     */
    public function exportAsXLSX(array $products): array
    {
        return $this->export($products, 'xlsx');
    }

    /**
     * Génération d'un export des entreprises au format xlsx.
     */
    public function exportAsCSV(array $products): array
    {
        return $this->export($products, 'csv');
    }

    /**
     * Export.
     */
    private function export(array $products, string $fileType): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->initializeHeader($sheet);
        $this->initializeBody($sheet, $products);

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
    private function initializeBody(Worksheet $sheet, array $products): void
    {
        $i = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A'.$i, $product->getTitle());
            $sheet->setCellValue('B'.$i, $product->getShortDescription());
            $sheet->setCellValue('C'.$i, $product->getAmount());
            $sheet->setCellValue('D'.$i, $product->getCaution());
            $sheet->setCellValue('E'.$i, $product->getQuantity());
            $sheet->setCellValue('F'.$i, $product->getQuantityAllReadyReserved());
            $sheet->setCellValue('G'.$i, $product->getAuthor()->getFullname());
            $sheet->setCellValue('H'.$i, $product->getDescription());
            $sheet->setCellValue('I'.$i, $product->getUpdatedAt()->format('d-m-Y'));
            $sheet->setCellValue('J'.$i, $product->getUpdatedAt()->format('d-m-Y'));
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
