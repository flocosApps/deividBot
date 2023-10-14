<?php

$csvUrl = 'https://docs.google.com/spreadsheets/u/1/d/1sVux6erzJoazsQuqSg6LCrmidfOF6zOeuIBMLFQwbsg/gviz/tq?tqx=out:csv&sheet=Dados';

$csvData = file_get_contents($csvUrl);

if ($csvData) {
    $lines = explode(PHP_EOL, $csvData);
    $json = [];

    if (count($lines) > 1) {
        $header = str_getcsv(array_shift($lines));
        foreach ($lines as $line) {
            $row = str_getcsv($line);
            $rowData = array_combine($header, $row);
            $json[] = $rowData;
        }
    }

    // Agora, você tem os dados da planilha em formato JSON organizado.
    echo json_encode($json, JSON_PRETTY_PRINT);
} else {
    echo "Não foi possível obter os dados da planilha.";
}

?>
