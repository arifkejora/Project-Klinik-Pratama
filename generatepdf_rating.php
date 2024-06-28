<?php
session_start();
include('db_connection.php');
require('fpdf/fpdf.php'); // Include FPDF library

if (!isset($_SESSION['login_pimpinan'])) {
    header("location: pages-login-doctor.php");
    exit;
}

// Function to generate PDF for rating
function generateRatingPDF($conn) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Klinik Pratama Anugerah Hexa Kudus', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, 'Jl. Protokol Tersono Rt. 01 Rw. 03 Garung Lor Tersono, Tersono, Garung Lor, Kec. Kaliwungu, Kabupaten Kudus, Jawa Tengah 59332 | Nomor Telepon: (0291) 430169', 0, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Laporan Rating', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(20, 5, 'ID Rating', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'ID Rekam Medis', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Rating', 1, 0, 'C', true);
    $pdf->Cell(0, 5, 'Ulasan', 1, 1, 'C', true);

    $sql = "SELECT * FROM rating";
    $result = $conn->query($sql);
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(20, 5, $row['id_rating'], 1, 0, 'C');
            $pdf->Cell(40, 5, $row['id_rekam_medis'], 1, 0, 'C');
            $pdf->Cell(20, 5, $row['rating'], 1, 0, 'C');
            $pdf->MultiCell(0, 5, $row['ulasan'], 1, 'L');
        }
    else:
        $pdf->Cell(0, 10, 'Tidak ada data rating', 1, 1, 'C');
    endif;

    $pdf->Output();
}

// Generate PDF for rating
generateRatingPDF($conn);
?>
