<?php
// Include database connection
include 'db.php';

// Include PDF library
require_once('tcpdf/tcpdf.php');

// Create new PDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Transaction Details PDF');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Retrieve transaction details from the database
$sql = "SELECT * FROM transaction";
$result = $conn->query($sql);

// Add transaction details to the PDF
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(0, 10, 'Transaction ID: ' . $row['transaction_id'], 0, 1);
        $pdf->Cell(0, 10, 'Medicine Name: ' . $row['medicine_name'], 0, 1);
        $pdf->Cell(0, 10, 'Customer Details: ' . $row['customer_details'], 0, 1);
        // Add more details as needed
        $pdf->Ln(); // Add a new line
    }
}

// Close and output PDF
$pdf->Output('transaction_details.pdf', 'D');
?>
