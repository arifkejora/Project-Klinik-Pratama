<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_rekam_medis'])) {
    $id = $_GET['id_rekam_medis'];

    $sql_rekam_medis = "SELECT rm.*, p.nama_pasien, p.email_pasien, p.nomorhp_pasien, dp.jenis_kelamin, dp.tanggal_lahir, dp.alamat_pasien
                        FROM rekam_medis rm
                        INNER JOIN antrian a ON rm.id_antrian = a.id_antrian
                        INNER JOIN pasien p ON a.id_pasien = p.id_pasien
                        INNER JOIN detail_pasien dp ON p.id_pasien = dp.id_pasien
                        WHERE rm.id_rekam_medis = ?";
    $stmt_rekam_medis = $conn->prepare($sql_rekam_medis);
    $stmt_rekam_medis->bind_param("i", $id);
    $stmt_rekam_medis->execute();
    $result_rekam_medis = $stmt_rekam_medis->get_result();
    $rekam_medis = $result_rekam_medis->fetch_assoc();

    // Jika tidak ada rekam medis dengan id yang diberikan, redirect ke halaman sebelumnya
    if (!$rekam_medis) {
        header("Location: doctor-history.php");
        exit;
    }

    // Query resep obat
    $sql_resep = "SELECT ro.id_resep, ro.id_obat, o.nama_obat 
                  FROM resep_obat ro 
                  INNER JOIN obat o ON ro.id_obat = o.id_obat 
                  WHERE ro.id_rekammedis = ?";
    $stmt_resep = $conn->prepare($sql_resep);
    $stmt_resep->bind_param("i", $id);
    $stmt_resep->execute();
    $result_resep = $stmt_resep->get_result();
    $resep_obat = [];
    while ($row_resep = $result_resep->fetch_assoc()) {
        $resep_obat[] = $row_resep;
    }

    $tanggal_lahir = new DateTime($rekam_medis['tanggal_lahir']);
    $today = new DateTime();
    $umur = $tanggal_lahir->diff($today);
    $umur_string = $umur->format('%y tahun %m bulan %d hari');
} else {
    // Jika tidak ada id_rekam_medis, redirect ke halaman sebelumnya
    header("Location: doctor-history.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Edit Data - Dokter</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Detail Pemeriksaan - RM00<?php echo htmlspecialchars($id); ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form class="row g-3" action="" method="POST">
                    <div class="col-md-4">
                            <label for="inputNamaPasien" class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control" id="inputNamaPasien" name="nama_pasien" value="<?php echo htmlspecialchars($rekam_medis['nama_pasien']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="inputEmailPasien" class="form-label">Email Pasien</label>
                            <input type="email" class="form-control" id="inputEmailPasien" name="email_pasien" value="<?php echo htmlspecialchars($rekam_medis['email_pasien']); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="inputNomorHpPasien" class="form-label">Nomor HP Pasien</label>
                            <input type="text" class="form-control" id="inputNomorHpPasien" name="nomorhp_pasien" value="<?php echo htmlspecialchars($rekam_medis['nomorhp_pasien']); ?>" readonly>
                        </div>

                    <div class="col-md-4">
                        <label for="inputJenisKelamin" class="form-label">Jenis Kelamin</label>
                        <input type="text" class="form-control" id="inputJenisKelamin" name="jenis_kelamin" value="<?php echo htmlspecialchars($rekam_medis['jenis_kelamin']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="inputTanggalLahir" class="form-label">Tanggal Lahir</label>
                        <input type="text" class="form-control" id="inputTanggalLahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($rekam_medis['tanggal_lahir']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="inputUmur" class="form-label">Umur</label>
                        <input type="text" class="form-control" id="inputUmur" name="umur" value="<?php echo htmlspecialchars($umur_string); ?>" readonly>
                    </div>
                    <div class="col-md-12">
                        <label for="inputAlamatPasien" class="form-label">Alamat Pasien</label>
                        <textarea class="form-control" id="inputAlamatPasien" name="alamat_pasien" rows="3" readonly><?php echo htmlspecialchars($rekam_medis['alamat_pasien']); ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputKeluhan" class="form-label">Keluhan</label>
                        <textarea class="form-control" id="inputKeluhan" name="keluhan" rows="3" required><?php echo htmlspecialchars($rekam_medis['keluhan']); ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputDiagnosa" class="form-label">Diagnosa</label>
                        <textarea class="form-control" id="inputDiagnosa" name="diagnosa" rows="3" required><?php echo htmlspecialchars($rekam_medis['diagnosa']); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="inputTekananDarah" class="form-label">Tekanan Darah</label>
                        <input type="text" class="form-control" id="inputTekananDarah" name="tekanan_darah" value="<?php echo htmlspecialchars($rekam_medis['tekanan_darah']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputBeratBadan" class="form-label">Berat Badan</label>
                        <input type="text" class="form-control" id="inputBeratBadan" name="berat_badan" value="<?php echo htmlspecialchars($rekam_medis['berat_badan']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputSuhuBadan" class="form-label">Suhu Badan</label>
                        <input type="text" class="form-control" id="inputSuhuBadan" name="suhu_badan" value="<?php echo htmlspecialchars($rekam_medis['suhu_badan']); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputHasilPemeriksaan" class="form-label">Hasil Pemeriksaan</label>
                        <textarea class="form-control" id="inputHasilPemeriksaan" name="hasil_pemeriksaan" rows="3" required><?php echo htmlspecialchars($rekam_medis['hasil_pemeriksaan']); ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputResep" class="form-label"><strong>Resep Obat</strong></label>
                        <?php if (!empty($resep_obat)): ?>
                            <ol type="1" class="list-unstyled">
                                <?php foreach ($resep_obat as $obat): ?>
                                    <li><?php echo htmlspecialchars($obat['nama_obat']); ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php else: ?>
                            <p>Tidak ada resep obat.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 text-center">
                        <a href="doctor-history.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
