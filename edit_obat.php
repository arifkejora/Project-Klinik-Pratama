<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_obat'])) {
    $id = $_GET['id_obat'];

    $sql = "SELECT * FROM obat WHERE id_obat = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
        $jenis_obat = mysqli_real_escape_string($conn, $_POST['jenis_obat']);
        $harga_obat = mysqli_real_escape_string($conn, $_POST['harga_obat']);

        $sql_update = "UPDATE obat SET nama_obat = '$nama_obat', jenis_obat = '$jenis_obat', harga_obat = '$harga_obat' WHERE id_obat = $id";

        if (mysqli_query($conn, $sql_update)) {
            header("Location: farmasi-obat.php");
            exit;
        } else {
            $message = "Error updating record: " . mysqli_error($conn);
        }
    }
} else {
    header("Location:  farmasi-obat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Edit Data - Obat</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Edit Data - Obat</h4>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form class="row g-3" action="" method="POST">
                    <div class="col-md-12">
                        <label for="inputNamaObat" class="form-label">Nama Obat</label>
                        <input type="text" class="form-control" id="inputNamaObat" name="nama_obat" value="<?php echo htmlspecialchars($row['nama_obat']); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputJenisObat" class="form-label">Jenis Obat</label>
                        <select class="form-control" id="inputJenisObat" name="jenis_obat" required>
                            <option value="">Pilih Jenis Obat</option>
                            <option value="Analgesik" <?php if ($row['jenis_obat'] == 'Analgesik') echo 'selected'; ?>>Analgesik</option>
                            <option value="Antibiotik" <?php if ($row['jenis_obat'] == 'Antibiotik') echo 'selected'; ?>>Antibiotik</option>
                            <option value="Antasida" <?php if ($row['jenis_obat'] == 'Antasida') echo 'selected'; ?>>Antasida</option>
                            <option value="Antidepresan" <?php if ($row['jenis_obat'] == 'Antidepresan') echo 'selected'; ?>>Antidepresan</option>
                            <option value="Antijamur" <?php if ($row['jenis_obat'] == 'Antijamur') echo 'selected'; ?>>Antijamur</option>
                            <option value="Antimalaria" <?php if ($row['jenis_obat'] == 'Antimalaria') echo 'selected'; ?>>Antimalaria</option>
                            <option value="Antipiretik" <?php if ($row['jenis_obat'] == 'Antipiretik') echo 'selected'; ?>>Antipiretik</option>
                            <option value="Antiseptik" <?php if ($row['jenis_obat'] == 'Antiseptik') echo 'selected'; ?>>Antiseptik</option>
                            <option value="Diuretik" <?php if ($row['jenis_obat'] == 'Diuretik') echo 'selected'; ?>>Diuretik</option>
                            <option value="Hipnotik" <?php if ($row['jenis_obat'] == 'Hipnotik') echo 'selected'; ?>>Hipnotik</option>
                            <option value="Imunomodulator" <?php if ($row['jenis_obat'] == 'Imunomodulator') echo 'selected'; ?>>Imunomodulator</option>
                            <option value="Laksatif" <?php if ($row['jenis_obat'] == 'Laksatif') echo 'selected'; ?>>Laksatif</option>
                            <option value="Obat Jantung" <?php if ($row['jenis_obat'] == 'Obat Jantung') echo 'selected'; ?>>Obat Jantung</option>
                            <option value="Obat Kanker" <?php if ($row['jenis_obat'] == 'Obat Kanker') echo 'selected'; ?>>Obat Kanker</option>
                            <option value="Obat Tidur" <?php if ($row['jenis_obat'] == 'Obat Tidur') echo 'selected'; ?>>Obat Tidur</option>
                            <option value="Psikotropika" <?php if ($row['jenis_obat'] == 'Psikotropika') echo 'selected'; ?>>Psikotropika</option>
                            <option value="Vitamin dan Suplemen" <?php if ($row['jenis_obat'] == 'Vitamin dan Suplemen') echo 'selected'; ?>>Vitamin dan Suplemen</option>
                            <option value="Herbal" <?php if ($row['jenis_obat'] == 'Herbal') echo 'selected'; ?>>Herbal</option>
                            <option value="Homeopati" <?php if ($row['jenis_obat'] == 'Homeopati') echo 'selected'; ?>>Homeopati</option>
                            <option value="Lainnya" <?php if ($row['jenis_obat'] == 'Lainnya') echo 'selected'; ?>>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="inputHargaObat" class="form-label">Harga Obat</label>
                        <input type="text" class="form-control" id="inputHargaObat" name="harga_obat" value="<?php echo htmlspecialchars($row['harga_obat']); ?>" required>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="farmasi-obat.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Tutup koneksi
mysqli_close($conn);
?>
