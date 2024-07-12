<?php
session_start();
include('db_connection.php');

$status_obat = '';
$message = '';

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

    if (!$rekam_medis) {
        header("Location: farmasi-riwayat.php");
        exit;
    }

    $sql_resep = "SELECT ro.id_resep, ro.id_obat, o.nama_obat, ro.status, ro.aturan, ro.keterangan
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

    if (!empty($resep_obat)) {
        $status_obat = $resep_obat[0]['status'];
    }
} else {
    header("Location: farmasi-riwayat.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'proses_obat') {
    foreach ($resep_obat as $obat) {
        $id_resep = $obat['id_resep'];
        $aturan = $_POST["aturan$id_resep"];
        $keterangan = $_POST["keterangan$id_resep"];

        $sql_update = "UPDATE resep_obat SET aturan = ?, keterangan = ? WHERE id_resep = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $aturan, $keterangan, $id_resep);
        $stmt_update->execute();
    }
    
    $sql_update_status = "UPDATE resep_obat SET status = 'Selesai' WHERE id_rekammedis = ?";
    $stmt_update_status = $conn->prepare($sql_update_status);
    $stmt_update_status->bind_param("i", $id);
    $stmt_update_status->execute();

    $message = "Status obat berhasil diubah.";
    header("Location: farmasi-riwayat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data - Pharmacist</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const processButton = document.getElementById('processButton');
            if (urlParams.has('status_changed') && urlParams.get('status_changed') === 'true') {
                processButton.textContent = 'Kembali';
                processButton.onclick = function() {
                    window.location.href = 'farmasi-riwayat.php';
                };
            } else {
                processButton.onclick = function() {
                    document.getElementById('processForm').submit();
                };
            }
        });
    </script>
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Edit Data - Pharmacist</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST" id="processForm">
                    <div class="row">
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
                    </div>

                    <div class="row mt-3">
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
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="inputAlamatPasien" class="form-label">Alamat Pasien</label>
                            <textarea class="form-control" id="inputAlamatPasien" name="alamat_pasien" rows="3" readonly><?php echo htmlspecialchars($rekam_medis['alamat_pasien']); ?></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="inputResep" class="form-label"><strong>Resep Obat</strong></label>
                            <?php if (!empty($resep_obat)): ?>
                                <ol type="1" class="list-unstyled">
                                    <?php foreach ($resep_obat as $obat): ?>
                                        <li>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label for="inputObat_<?php echo $obat['id_resep']; ?>" class="form-label">Nama Obat</label>
                                                    <input type="text" class="form-control" id="inputObat_<?php echo $obat['id_resep']; ?>" name="nama_obat_<?php echo $obat['id_resep']; ?>" value="<?php echo htmlspecialchars($obat['nama_obat']); ?>" readonly>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="inputAturan_<?php echo $obat['id_resep']; ?>" class="form-label">Aturan</label>
                                                    <input type="text" class="form-control" id="inputAturan_<?php echo $obat['id_resep']; ?>" name="aturan_<?php echo $obat['id_resep']; ?>" value="<?php echo htmlspecialchars($obat['aturan']); ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="inputKeterangan_<?php echo $obat['id_resep']; ?>" class="form-label">Keterangan</label>
                                                    <input type="text" class="form-control" id="inputKeterangan_<?php echo $obat['id_resep']; ?>" name="keterangan_<?php echo $obat['id_resep']; ?>" value="<?php echo htmlspecialchars($obat['keterangan']); ?>">
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p>Tidak ada resep obat.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <input type="hidden" name="action" value="proses_obat">
                            <button type="submit" class="btn btn-primary" id="processButton">
                                <?php echo ($status_obat === 'Selesai') ? 'Kembali' : 'Proses Obat'; ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
