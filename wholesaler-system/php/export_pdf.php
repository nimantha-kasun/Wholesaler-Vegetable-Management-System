<?php
require 'db.php';
require 'fpdf/fpdf.php';

// Create PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Title
$pdf->Cell(0, 10, 'Stock Summary Report', 0, 1, 'C');
$pdf->Ln(5);

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(50, 10, 'Farmer', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Contact', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Vegetable', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Qty', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Price', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Date', 1, 1, 'C', true);


// Fetch data
$sql = "SELECT f.name AS farmer, f.contact AS contact, v.name AS vegetable, s.quantity, s.price, s.date
        FROM stocks s
        JOIN farmers f ON s.farmer_id = f.farmer_id
        JOIN vegetables v ON s.veg_id = v.veg_id
        ORDER BY s.date DESC";
$result = $conn->query($sql);

// Table rows
$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, utf8_decode(substr($row['farmer'], 0, 25)), 1);
    // $pdf->Cell(50, 10, '' . $row['farmer'], 1);
    $pdf->Cell(35, 10, ' ' . $row['contact'], 1);
    $pdf->Cell(35, 10, ' ' . $row['vegetable'], 1);
    $pdf->Cell(20, 10, ' ' . $row['quantity'] . 'kg', 1);
    $pdf->Cell(20, 10, ' Rs. ' . $row['price'], 1);
    $pdf->Cell(35, 10, ' ' . $row['date'], 1);
    
    $pdf->Ln();

}

$pdf->Output('D', 'stock_summary_report.pdf'); // D = force download
?>
