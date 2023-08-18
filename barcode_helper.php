<?php

require 'vendor/autoload.php'; // Adjust the path to autoload.php

use Zend\Barcode\Barcode;

function generate_barcode_image($box_number) {
    $rendererOptions = array(
        'imageType' => 'png',
        'horizontalPosition' => 'center',
        'verticalPosition' => 'middle',
    );
    
    $barcodeOptions = array(
        'text' => $box_number,
        'barHeight' => 50,
        'drawText' => true,
        'font' => 5,
    );
    
    $barcodeImage = Barcode::draw('code128', 'image', $barcodeOptions, $rendererOptions);
    
    $barcodeImagePath = 'path/to/your/barcodes/' . $box_number . '.png'; // Adjust the path
    
    imagepng($barcodeImage, $barcodeImagePath);
    
    return $barcodeImagePath;
}
