<?php
require('fpdf/fpdf.php'); 
include('db_connection.php');

function convertToIndonesianDate($date) {
    $months = array(
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    );

    $dateObj = new DateTime($date);
    $month = $months[$dateObj->format('F')];
    $day = $dateObj->format('d');
    $year = $dateObj->format('Y');

    return $day . ' ' . $month . ' ' . $year;
}

// Ambil data pasien dari database
$id_rujukan = $_GET['id_rujukan']; // Ambil id_rujukan dari URL
$id_rekam_medis = $_GET['id_rekam_medis']; // Ambil id_rekam_medis dari URL
$sql_rekam_medis = "SELECT rm.*, p.nama_pasien, p.email_pasien, p.nomorhp_pasien, dp.jenis_kelamin, dp.tanggal_lahir, dp.alamat_pasien, r.nama_rumahsakit, r.tanggal_rujukan
                    FROM rekam_medis rm
                    INNER JOIN antrian a ON rm.id_antrian = a.id_antrian
                    INNER JOIN pasien p ON a.id_pasien = p.id_pasien
                    INNER JOIN detail_pasien dp ON p.id_pasien = dp.id_pasien
                    LEFT JOIN rujukan r ON rm.id_rekam_medis = r.id_rekammedis
                    WHERE rm.id_rekam_medis = ?";
$stmt = $conn->prepare($sql_rekam_medis);
$stmt->bind_param('i', $id_rekam_medis);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Inisialisasi FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15); 

    // Header surat
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Klinik Pratama Anugerah Hexa Kudus', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCell(0, 5, 'Jl. Protokol Tersono Rt. 01 Rw. 03 Garung Lor Tersono, Tersono, Garung Lor, Kec. Kaliwungu, Kabupaten Kudus, Jawa Tengah 59332 | Nomor Telepon: (0291) 430169', 0, 'C');
    $pdf->Ln(5);

    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(5); 

    // Set font untuk judul
    $pdf->SetFont('Arial', 'B', 14);

    // Judul
    $pdf->Cell(0, 10, 'SURAT RUJUKAN', 0, 1, 'C');

    // Set font untuk teks biasa
    $pdf->SetFont('Arial', '', 12);

    // Tanggal
    $pdf->Cell(0, 10, 'Kudus, ' . convertToIndonesianDate(date('Y-m-d')), 0, 1, 'R');

    // Nomor surat dan perihal
    $pdf->MultiCell(0, 7, 'Nomor : 440/103.6873/' . ($row['id_rekam_medis'] ?? ''), 0, 'L');
    $pdf->MultiCell(0, 7, 'Perihal : Surat Pengantar Rujukan', 0, 'L');
    $pdf->Ln(5);

    // Kepada Yth.
    $pdf->MultiCell(0, 7, 'Kepada Yth.', 0, 'L');
    $pdf->MultiCell(0, 7, ($row['nama_rumahsakit'] ?? ''), 0, 'L');
    $pdf->Ln(5);

    // Dengan hormat
    $pdf->MultiCell(0, 7, 'Dengan hormat,', 0, 'J');
    $pdf->MultiCell(0, 7, 'Bersama ini kami kirimkan pasien yang bernama:', 0, 'J');
    $pdf->Cell(40, 7, '         Nama', 0, 0, 'L');
    $pdf->Cell(0, 7, ': ' . ($row['nama_pasien'] ?? ''), 0, 1, 'L');
    $pdf->Cell(40, 7, '         Umur', 0, 0, 'L');
    $pdf->Cell(0, 7, ': ' . calculateAge($row['tanggal_lahir'] ?? ''), 0, 1, 'L');
    $pdf->Cell(40, 7, '         Jekel', 0, 0, 'L');
    $pdf->Cell(0, 7, ': ' . ($row['jenis_kelamin'] ?? ''), 0, 1, 'L');
    $pdf->Cell(40, 7, '         Alamat', 0, 0, 'L');
    $pdf->Cell(0, 7, ': ' . ($row['alamat_pasien'] ?? ''), 0, 1, 'L');
    $pdf->Ln(5);

    // Keluhan dan Diagnosis
    $pdf->MultiCell(0, 7, 'Pada pemeriksaan yang kami lakukan, ditemukan:', 0, 'J');
    $pdf->Cell(40, 7, '         Keluhan', 0, 0, 'L');
    $pdf->Cell(0, 7, ': ' . ($row['keluhan'] ?? ''), 0, 1, 'L');
    $pdf->Cell(40, 7, '         Diagnosa', 0, 0, 'L');
    $pdf->Cell(0, 7, ': ' . ($row['diagnosa'] ?? ''), 0, 1, 'L');
    

    $pdf->Ln(5);
    // Penutup
    $pdf->MultiCell(0, 7, 'Demikian kami sampaikan, mohon konsultasi dan perawatan selanjutnya. Atas bantuannya dan kerjasama, kami ucapkan terima kasih.', 0, 'J');
    $pdf->Ln(10);

    // Tanggal dan nama klinik
    $pdf->Cell(0, 7, 'Kudus, ' . convertToIndonesianDate(date('Y-m-d')), 0, 1, 'R');
    $pdf->Cell(0, 7, 'Hormat Kami', 0, 1, 'R');
    $pdf->Ln(10);

    // Tanda tangan dokter
    $pdf->Cell(0, 10, 'Klinik Pratama Anugerah Hexa', 0, 1, 'R');

    // Output PDF
    $pdf->Output('print_rujukan.pdf', 'I');
} else {
    echo "Data rekam medis tidak ditemukan.";
}

$stmt->close();
$conn->close();


function calculateAge($birthdate) {
    if (!$birthdate) return '';

    $dob = new DateTime($birthdate);
    $now = new DateTime();
    $age = $now->diff($dob);
    return $age->y . ' tahun';
}
?>
