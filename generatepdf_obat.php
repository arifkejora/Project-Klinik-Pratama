<?php
session_start();
include('db_connection.php');
require('fpdf/fpdf.php'); // Include FPDF library

if (!isset($_SESSION['login_pimpinan'])) {
    header("location: pages-login-doctor.php");
    exit;
}

$type = $_GET['type'];

// Function to generate PDF for medications
function generateMedicationPDF($conn) {
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
    $pdf->Cell(0, 10, 'Laporan Obat', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(20, 5, 'ID Obat', 1, 0, 'C', true);
    $pdf->Cell(60, 5, 'Nama Obat', 1, 0, 'C', true);
    $pdf->Cell(40, 5, 'Jenis Obat', 1, 0, 'C', true);
    $pdf->Cell(0, 5, 'Harga Obat', 1, 1, 'C', true);

    $sql = "SELECT * FROM obat";
    $result = $conn->query($sql);
    if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(20, 5, $row['id_obat'], 1, 0, 'C');
            $pdf->Cell(60, 5, $row['nama_obat'], 1, 0);
            $pdf->Cell(40, 5, $row['jenis_obat'], 1, 0);
            $pdf->Cell(0, 5, 'Rp ' . number_format($row['harga_obat'], 2, ',', '.'), 1, 1, 'C');
        }
    else:
        $pdf->Cell(160, 10, 'Tidak ada data obat', 1, 1, 'C');
    endif;

    $pdf->Output();
}

// Generate PDF based on type
if ($type === 'obat') {
    generateMedicationPDF($conn);
}
?>
