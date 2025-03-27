<?php
require '../vendor/autoload.php'; // Load dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans'); // More compatible font
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Allow remote images

$dompdf = new Dompdf($options);

ob_start();
include('bill-generate.php'); // Your invoice HTML file
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


// Save PDF to a file instead of forcing download
$pdfOutput = $dompdf->output();
$filePath = 'invoices/invoice_' . time() . '.pdf'; // Save with timestamp
file_put_contents($filePath, $pdfOutput);
// Output the PDF for download

header("Location:".$filePath);
exit;
?>
