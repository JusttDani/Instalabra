<?php
$url = "https://raw.githubusercontent.com/javierarce/palabras/master/listado-general.txt";
$content = file_get_contents($url);
if ($content === false) {
    die("Error al descargar el diccionario.\n");
}
$words = explode("\n", $content);

$valid = [];
$replacements = [
    'á' => 'a',
    'é' => 'e',
    'í' => 'i',
    'ó' => 'o',
    'ú' => 'u',
    'ü' => 'u',
    'Á' => 'A',
    'É' => 'E',
    'Í' => 'I',
    'Ó' => 'O',
    'Ú' => 'U',
    'Ü' => 'U'
];

foreach ($words as $w) {
    $w = trim($w);
    if (empty($w))
        continue;

    // Remove accents but keep ñ
    $w_no_accents = strtr($w, $replacements);
    $w_upper = mb_strtoupper($w_no_accents, 'UTF-8');

    // Check if it's 5 letters and only contains A-Z and Ñ
    if (mb_strlen($w_upper, 'UTF-8') === 5 && preg_match('/^[A-ZÑ]+$/u', $w_upper)) {
        $valid[$w_upper] = true;
    }
}

ksort($valid);
$output = implode("\n", array_keys($valid));
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data');
}
file_put_contents(__DIR__ . '/data/palabras_5.txt', $output);
echo "Generadas " . count($valid) . " palabras.\n";
