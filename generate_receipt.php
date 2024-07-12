<?php
require('fpdf/fpdf.php');
include('db_connection.php'); 

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'Klinik Pratama Anugerah Hexa', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 5, 'Jl. Protokol Tersono Rt. 01 Rw. 03', 0, 1, 'C');
        $this->Cell(0, 5, 'Garung Lor Tersono, Tersono, Garung Lor', 0, 1, 'C');
        $this->Cell(0, 5, 'Kec. Kaliwungu, Kabupaten Kudus, Jawa Tengah 59332', 0, 1, 'C');
        $this->Ln(10); 
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function Content($patientDetails, $doctorFee, $medicationCost, $totalCost, $prescriptions) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Detail Pasien:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 10, 'Nama:', 0, 0);
        $this->Cell(0, 10, $patientDetails['nama_pasien'], 0, 1);
        $this->Cell(60, 10, 'Alamat:', 0, 0);
        $this->Cell(0, 10, $patientDetails['alamat_pasien'], 0, 1);
        $this->Cell(60, 10, 'Hasil Pemeriksaan:', 0, 0);
        $this->Cell(0, 10, ($patientDetails['hasil_pemeriksaan'] ?? 'Data tidak tersedia'), 0, 1);

        $this->Ln(10);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Resep Obat dari Dokter:', 0, 1);
        $this->SetFont('Arial', '', 12);
        foreach ($prescriptions as $prescription) {
            $this->Cell(0, 10, '- ' . $prescription['nama_obat'] . ' (Rp. ' . number_format($prescription['harga_obat'], 0, ',', '.') . ')', 0, 1);
        }

        $this->Ln(10);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Rincian Biaya:', 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(60, 10, 'Biaya Dokter:', 0, 0);
        $this->Cell(0, 10, 'Rp. ' . number_format($doctorFee, 0, ',', '.'), 0, 1);
        $this->Cell(60, 10, 'Total Harga Obat:', 0, 0);
        $this->Cell(0, 10, 'Rp. ' . number_format($medicationCost, 0, ',', '.'), 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(60, 10, 'Total Biaya:', 0, 0);
        $this->Cell(0, 10, 'Rp. ' . number_format($totalCost, 0, ',', '.'), 0, 1);

        $this->SetLineWidth(0.5);
        $this->Line(10, 40, 200, 40);
    }
}

$id_rekam_medis = $_GET['id_rekam_medis']; 
$sql = "SELECT p.nama_pasien, d.alamat_pasien, rm.hasil_pemeriksaan 
        FROM pasien p 
        INNER JOIN detail_pasien d ON p.id_pasien = d.id_pasien
        JOIN antrian a ON p.id_pasien = a.id_pasien
        JOIN rekam_medis rm ON a.id_antrian = rm.id_antrian
        WHERE rm.id_rekam_medis = $id_rekam_medis"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $patientDetails = array(
        'nama_pasien' => $row['nama_pasien'],
        'alamat_pasien' => $row['alamat_pasien'],
        'hasil_pemeriksaan' => $row['hasil_pemeriksaan'] 
    );

    $sql2 = "SELECT obat.nama_obat, obat.harga_obat
             FROM resep_obat
             INNER JOIN obat ON resep_obat.id_obat = obat.id_obat
             WHERE resep_obat.id_rekammedis = $id_rekam_medis";

    $result2 = $conn->query($sql2);
    $prescriptions = array();
    $totalObat = 0;

    if ($result2->num_rows > 0) {
        while ($row2 = $result2->fetch_assoc()) {
            $prescriptions[] = $row2;
            $totalObat += $row2['harga_obat'];
        }
    }

    $sql3 = "SELECT dokter.harga_perkunjungan
             FROM rekam_medis
             INNER JOIN antrian ON rekam_medis.id_antrian = antrian.id_antrian
             INNER JOIN jadwal_dokter ON antrian.id_jadwal = jadwal_dokter.id_jadwal
             INNER JOIN dokter ON jadwal_dokter.id_dokter = dokter.id_dokter
             WHERE rekam_medis.id_rekam_medis = $id_rekam_medis";

    $result3 = $conn->query($sql3);
    $doctorFee = 0;

    if ($result3->num_rows > 0) {
        $row3 = $result3->fetch_assoc();
        $doctorFee = $row3['harga_perkunjungan'];
    }

    $totalCost = $doctorFee + $totalObat;
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->Content($patientDetails, $doctorFee, $totalObat, $totalCost, $prescriptions);
    $pdf->Output();
} else {
    echo "Data pasien tidak ditemukan."; 
}
?>
