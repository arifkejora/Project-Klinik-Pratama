<?php
session_start();
include('db_connection.php');
require('fpdf/fpdf.php'); // Include FPDF library

if (!isset($_SESSION['login_pimpinan'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$type = $_GET['type'];

// Function to generate PDF for medical records
function generateMedicalRecordsPDF($conn) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Klinik Pratama Anugerah Hexa Kudus', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, 'Jl. Protokol Tersono Rt. 01 Rw. 03 Garung Lor Tersono, Tersono, Garung Lor, Kec. Kaliwungu, Kabupaten Kudus, Jawa Tengah 59332 | Nomor Telepon: (0291) 430169', 0, 'C');
    $pdf->Ln(5);

    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Rekap Rekam Medis', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(5, 5, 'ID', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Keluhan', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Diagnosa', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Tekanan Darah', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Berat Badan', 1, 0, 'C', true);
    $pdf->Cell(20, 5, 'Suhu Badan', 1, 0, 'C', true);
    $pdf->Cell(0, 5, 'Hasil Pemeriksaan', 1, 0, 'C', true);

    $sql = "SELECT * FROM rekam_medis";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(5, 5, $row['id_rekam_medis'], 1, 0, 'C');
    
            // MultiCell for Keluhan
            $pdf->MultiCell(40, 5, $row['keluhan'], 1, 'L');
    
            // MultiCell for Diagnosa
            $pdf->MultiCell(40, 5, $row['diagnosa'], 1, 'L');
    
            $pdf->Cell(20, 5, $row['tekanan_darah'], 1, 0, 'C');
            $pdf->Cell(20, 5, $row['berat_badan'], 1, 0, 'C');
            $pdf->Cell(20, 5, $row['suhu_badan'], 1, 0, 'C');
    
            // MultiCell for Hasil Pemeriksaan
            $pdf->MultiCell(30, 5, $row['hasil_pemeriksaan'], 1, 'L');
    
        }
    } else {
        $pdf->Cell(200, 10, 'Tidak ada data rekam medis', 1, 1, 'C');
    }
    
    $pdf->Output();
    

    $pdf->Output();
}

// Generate PDF based on type
if ($type === 'rekammedis') {
    generateMedicalRecordsPDF($conn);
}
?>
