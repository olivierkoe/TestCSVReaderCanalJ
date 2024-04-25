<?php
namespace App\Service;

use Symfony\Component\Csv\CsvReader;

class CsvReaderService
{
    public function readCsvFile(string $filePath): array
    {
        $csvReader = new CsvReader
        ();
        $csvReader->setDelimiter(';'); // Si votre fichier CSV utilise un délimiteur différent

        $records = [];

        // Ouvrir le fichier CSV et lire ses lignes
        $csvReader->open($filePath);
        foreach ($csvReader as $record) {
            $records[] = $record;
        }
        $csvReader->close();

        return $records;
    }
}

