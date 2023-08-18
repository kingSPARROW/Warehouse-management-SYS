<?php
require_once('includes/load.php');
require('vendor/autoload.php'); // Make sure to include the autoload for barcode generation

function generate_barcode($tray_id) {
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    $barcodeData = $generator->getBarcode($tray_id, $generator::TYPE_CODE_128);
    $barcodeFilePath = 'barcode/' . $tray_id . '.png';
    file_put_contents($barcodeFilePath, $barcodeData);
    return $barcodeFilePath;
}
?>
