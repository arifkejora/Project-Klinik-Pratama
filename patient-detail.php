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
    $stmt_rekam_medis->bind_param("s", $id);
    $stmt_rekam_medis->execute();
    $result_rekam_medis = $stmt_rekam_medis->get_result();
    $rekam_medis = $result_rekam_medis->fetch_assoc();

    if (!$rekam_medis) {
        header("Location: patient-checkup.php");
        exit;
    }

    $sql_resep = "SELECT ro.id_resep, ro.id_obat, o.nama_obat, ro.status, ro.aturan, ro.keterangan, o.jenis_obat
                  FROM resep_obat ro 
                  INNER JOIN obat o ON ro.id_obat = o.id_obat 
                  WHERE ro.id_rekammedis = ?";
    $stmt_resep = $conn->prepare($sql_resep);
    $stmt_resep->bind_param("s", $id);
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
    header("Location: patient-checkup.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $ulasan = $_POST['ulasan'];
    $id_rekam_medis = $_POST['id_rekam_medis'];
    $role = $_POST['role'];
    $id_antrian = $rekam_medis['id_antrian']; // Assuming id_antrian is available from $rekam_medis

    $sql_update_rating = "";
    if ($role == 'admin') {
        $sql_update_rating = "UPDATE rekam_medis SET rate_admin = ?, ulasan_admin = ? WHERE id_rekam_medis = ? AND id_antrian = ?";
    } elseif ($role == 'dokter') {
        $sql_update_rating = "UPDATE rekam_medis SET rate_dokter = ?, ulasan_dokter = ? WHERE id_rekam_medis = ? AND id_antrian = ?";
    } elseif ($role == 'farmasi') {
        $sql_update_rating = "UPDATE rekam_medis SET rate_farmasi = ?, ulasan_farmasi = ? WHERE id_rekam_medis = ? AND id_antrian = ?";
    }

    if ($sql_update_rating) {
        $stmt_update_rating = $conn->prepare($sql_update_rating);
        $stmt_update_rating->bind_param("isss", $rating, $ulasan, $id_rekam_medis, $id_antrian);

        if ($stmt_update_rating->execute()) {
            $message = "Rating and review submitted successfully.";
        } else {
            $message = "Failed to submit rating and review.";
        }
    }
}

$sql_rating = "SELECT rate_admin, rate_dokter, rate_farmasi, ulasan_admin, ulasan_dokter, ulasan_farmasi 
               FROM rekam_medis WHERE id_rekam_medis = ?";
$stmt_rating = $conn->prepare($sql_rating);
$stmt_rating->bind_param("s", $id);
$stmt_rating->execute();
$result_rating = $stmt_rating->get_result();
$ratings = $result_rating->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemeriksaan</title>
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse; /* Urutan bintang dari kanan ke kiri */
        }

        .rating .star {
            display: inline-block;
            font-size: 24px;
            cursor: pointer;
            color: #ccc;
        }

        .rating .star:hover,
        .rating .star.active,
        .rating .star:hover ~ .star {
            color: #FFD700; /* warna bintang saat dihover dan yang dipilih */
        }
    </style>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Detail Pemeriksaan</h4>
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
                                                <div class="col-md-6">
                                                    <strong>Nama Obat:</strong> <?php echo htmlspecialchars($obat['nama_obat']); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Status:</strong> <?php echo htmlspecialchars($obat['status']); ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <strong>Aturan:</strong> <?php echo htmlspecialchars($obat['aturan']); ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <strong>Keterangan:</strong> <?php echo htmlspecialchars($obat['keterangan']); ?>
                                                </div>
                                                <div class="col-md-12">
                                                    <strong>Jenis Obat:</strong> <?php echo htmlspecialchars($obat['jenis_obat']); ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php else: ?>
                                <p>Tidak ada resep obat untuk pasien ini.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="inputRole" class="form-label">Pilih Role</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin" required>
                                <label class="form-check-label" for="roleAdmin">
                                    Admin
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleDokter" value="dokter">
                                <label class="form-check-label" for="roleDokter">
                                    Dokter
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="roleFarmasi" value="farmasi">
                                <label class="form-check-label" for="roleFarmasi">
                                    Farmasi
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-0">
                            <label for="inputRating" class="form-label">Rating</label>
                            <div class="rating">
                                <span class="star" data-value="5">&#9733;</span>
                                <span class="star" data-value="4">&#9733;</span>
                                <span class="star" data-value="3">&#9733;</span>
                                <span class="star" data-value="2">&#9733;</span>
                                <span class="star" data-value="1">&#9733;</span>
                            </div>
                            <input type="hidden" name="rating" id="inputRating" value="">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="inputUlasan" class="form-label">Ulasan</label>
                            <textarea class="form-control" id="inputUlasan" name="ulasan" rows="3" required></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="id_rekam_medis" value="<?php echo htmlspecialchars($id); ?>">

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Kirim Rating dan Ulasan</button>
                        </div>
                    </div>
                </form>
                <table class="table">
            <thead>
                <tr>
                    <th>Admin</th>
                    <th>Dokter</th>
                    <th>Farmasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($ratings['rate_admin']); ?> Bintang</td>
                    <td><?php echo htmlspecialchars($ratings['rate_dokter']); ?> Bintang</td>
                    <td><?php echo htmlspecialchars($ratings['rate_farmasi']); ?> Bintang</td>
                </tr>
                <tr>
                    <td><?php echo htmlspecialchars($ratings['ulasan_admin']); ?></td>
                    <td><?php echo htmlspecialchars($ratings['ulasan_dokter']); ?></td>
                    <td><?php echo htmlspecialchars($ratings['ulasan_farmasi']); ?></td>
                </tr>
            </tbody>
        </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('inputRating');

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = star.getAttribute('data-value');
                    ratingInput.value = value;
                    updateStars(value);
                });
            });

            function updateStars(value) {
                stars.forEach(star => {
                    if (star.getAttribute('data-value') <= value) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>

</html>
