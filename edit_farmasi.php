<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_farmasi'])) {
    $id = $_GET['id_farmasi'];

    $sql = "SELECT * FROM farmasi WHERE id_farmasi = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nip = mysqli_real_escape_string($conn, $_POST['nip']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $startdate = mysqli_real_escape_string($conn, $_POST['startdate']);

        $sql = "UPDATE farmasi SET NIP = '$nip', nama_farmasi = '$name', email_farmasi = '$email', alamat_farmasi = '$address', mulai_bekerja = '$startdate' WHERE id_farmasi = $id";

        if (mysqli_query($conn, $sql)) {
            header("Location: admin_farmasi.php");
        } else {
            $message = "Error updating record: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: admin_farmasi.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Edit Data - Pharmacist</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Edit Data - Pharmacist</h4>
            </div>
            <div class="card-body">
                <?php if (isset($message)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form class="row g-3" action="" method="POST">
                    <div class="col-md-12">
                        <label for="inputNip5" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="inputNip5" name="nip" value="<?php echo $row['nip']; ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="inputName5" name="name" value="<?php echo $row['nama_farmasi']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="inputEmail5" class="form-label">Email</label>
                        <input type="email" class="form-control" id="inputEmail5" name="email" value="<?php echo $row['email_farmasi']; ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="inputAddress5" class="form-label">Alamat Lengkap</label>
                        <input type="text" class="form-control" id="inputAddress5" name="address" value="<?php echo $row['alamat_farmasi']; ?>" placeholder="Jl. Kencana" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputStartDate" class="form-label">Tanggal Mulai Bekerja</label>
                        <input type="date" class="form-control" id="inputStartDate" name="startdate" value="<?php echo $row['mulai_bekerja']; ?>" required>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="admin_farmasi.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
