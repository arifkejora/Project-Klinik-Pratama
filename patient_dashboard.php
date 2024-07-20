<?php
session_start();
include('db_connection.php'); 

if (!isset($_SESSION['login_user'])) {
    header("location: pages-login.php");
    exit;
}

function generateId($conn) {
    $sql = "SELECT id_detail_pasien FROM detail_pasien ORDER BY id_detail_pasien DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['id_detail_pasien'];
        $lastNumber = intval(substr($lastId, 3)); // Changed to get numbers after 'DPS'
        $newNumber = $lastNumber + 1;
        return 'DPS' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    } else {
        return 'DPS01'; // First ID if the table is empty
    }
}


// Load JSON data
$provinsi_data = json_decode(file_get_contents('data/provinsi.json'), true);
$kabupaten_data = json_decode(file_get_contents('data/kabupaten.json'), true);
$kecamatan_data = json_decode(file_get_contents('data/kecamatan.json'), true);
$desa_data = json_decode(file_get_contents('data/desa.json'), true);

$id_patient = $_SESSION['login_id'];
$sql = "SELECT * FROM detail_pasien WHERE id_pasien = '$id_patient'";
$result = mysqli_query($conn, $sql);
$details = mysqli_fetch_assoc($result);

$details_missing = false;
if (!$details) {
    $details_missing = true;
} else {
    $gender = isset($details['jenis_kelamin']) ? $details['jenis_kelamin'] : '';
    $birth_date = isset($details['tanggal_lahir']) ? $details['tanggal_lahir'] : '';
    $address = isset($details['alamat_pasien']) ? $details['alamat_pasien'] : '';
    $provinsi_id = isset($details['provinsi']) ? $details['provinsi'] : '';
    $kabupaten_id = isset($details['kabupaten']) ? $details['kabupaten'] : '';
    $kecamatan_id = isset($details['kecamatan']) ? $details['kecamatan'] : '';
    $desa_id = isset($details['desa']) ? $details['desa'] : '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];
    $address = $_POST['address'];
    $provinsi_id = $_POST['province'];
    $kabupaten_id = $_POST['city'];
    $kecamatan_id = $_POST['district'];
    $desa_id = $_POST['subdistrict'];

    // To protect against MySQL injection
    $gender = mysqli_real_escape_string($conn, $gender);
    $birth_date = mysqli_real_escape_string($conn, $birth_date);
    $address = mysqli_real_escape_string($conn, $address);
    $provinsi_id = mysqli_real_escape_string($conn, $provinsi_id);
    $kabupaten_id = mysqli_real_escape_string($conn, $kabupaten_id);
    $kecamatan_id = mysqli_real_escape_string($conn, $kecamatan_id);
    $desa_id = mysqli_real_escape_string($conn, $desa_id);

    $newId = generateId($conn);

    // Check if the detail already exists for the patient
    $check_sql = "SELECT * FROM detail_pasien WHERE id_pasien = '$id_patient'";
    $check_result = mysqli_query($conn, $check_sql);
    $count = mysqli_num_rows($check_result);

    if ($count == 1) {
        // Update existing detail
        $update_sql = "UPDATE detail_pasien SET jenis_kelamin = '$gender', tanggal_lahir = '$birth_date', alamat_pasien = '$address', provinsi = '$provinsi_id', kabupaten = '$kabupaten_id', kecamatan = '$kecamatan_id', desa = '$desa_id' WHERE id_pasien = '$id_patient'";
        $update_result = mysqli_query($conn, $update_sql);
        
        if ($update_result) {
            $message = "Detail pasien berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui detail pasien: " . mysqli_error($conn);
        }
    } else {
        // Insert new detail
        $insert_sql = "INSERT INTO detail_pasien (id_detail_pasien, id_pasien, jenis_kelamin, tanggal_lahir, alamat_pasien, provinsi, kabupaten, kecamatan, desa) VALUES ('$newId', '$id_patient', '$gender', '$birth_date', '$address', '$provinsi_id', '$kabupaten_id', '$kecamatan_id', '$desa_id')";
        $insert_result = mysqli_query($conn, $insert_sql);
        
        if ($insert_result) {
            $message = "Detail pasien berhasil disimpan.";
        } else {
            $error = "Gagal menyimpan detail pasien: " . mysqli_error($conn);
        }
    }
}

// Function to get province name by ID
function getProvinceName($id, $provinsi_data) {
    foreach ($provinsi_data as $province) {
        if ($province['id'] == $id) {
            return $province['name'];
        }
    }
    return '';
}

// Function to get kabupaten name by ID
function getKabupatenName($id, $kabupaten_data) {
    foreach ($kabupaten_data as $kabupaten) {
        if ($kabupaten['id'] == $id) {
            return $kabupaten['name'];
        }
    }
    return '';
}

// Function to get kecamatan name by ID
function getKecamatanName($id, $kecamatan_data) {
    foreach ($kecamatan_data as $kecamatan) {
        if ($kecamatan['id'] == $id) {
            return $kecamatan['name'];
        }
    }
    return '';
}

// Function to get desa name by ID
function getDesaName($id, $desa_data) {
    foreach ($desa_data as $desa) {
        if ($desa['id'] == $id) {
            return $desa['desa'];
        }
    }
    return '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard Pasien</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">Pratama</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->


    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $_SESSION['login_user']; ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            <h6><?php echo $_SESSION['login_user']; ?></h6>
              <span>Admin</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout-patient.php">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
              </a>
            </li>
          
          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="patient_dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="patient-checkup.php">
          <i class="bi bi-person"></i>
          <span>Periksa</span>
        </a>
      </li>

      <!-- <li class="nav-item">
        <a class="nav-link collapsed" href="patient-history.php">
          <i class="bi bi-journal-text"></i>
          <span>Riwayat Periksa</span>
        </a>
      </li> -->

    </ul>
  </aside>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Beranda</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">

        <div class="col-xl-12">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Detail Profil</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profil</button>
                </li>
<!-- 
                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li> -->

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  <!-- <h5 class="card-title">About</h5>
                  <p class="small fst-italic">Sunt est soluta temporibus accusantium neque nam maiores cumque temporibus. Tempora libero non est unde veniam est qui dolor. Ut sunt iure rerum quae quisquam autem eveniet perspiciatis odit. Fuga sequi sed ea saepe at unde.</p> -->

                  <h5 class="card-title">Detail Profil</h5>

                  <?php if ($details_missing) { ?>
                                        <div class="alert alert-warning" role="alert">
                                            Harap Lengkapi Data Terlebih Dahulu di menu Edit Profil
                                        </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Nama Lengkap</div>
                                        <div class="col-lg-9 col-md-8"><?php echo $_SESSION['login_user']; ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Jenis Kelamin</div>
                                        <div class="col-lg-9 col-md-8"><?php echo $details_missing ? '' : $gender; ?></div>
                                    </div>

                                    <div class="row">
                                      <div class="col-lg-3 col-md-4 label">Tanggal Lahir</div>
                                      <div class="col-lg-9 col-md-8"><?php echo $details_missing ? '' : date('d-m-Y', strtotime($birth_date)); ?></div>
                                  </div>
                                  <div class="row">
    <div class="col-lg-3 col-md-4 label">Provinsi</div>
    <div class="col-lg-9 col-md-8">
        <?php echo isset($provinsi_id) ? getProvinceName($provinsi_id, $provinsi_data) : ''; ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-4 label">Kabupaten</div>
    <div class="col-lg-9 col-md-8">
        <?php echo isset($kabupaten_id) ? getKabupatenName($kabupaten_id, $kabupaten_data) : ''; ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-4 label">Kecamatan</div>
    <div class="col-lg-9 col-md-8">
        <?php echo isset($kecamatan_id) ? getKecamatanName($kecamatan_id, $kecamatan_data) : ''; ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3 col-md-4 label">Desa</div>
    <div class="col-lg-9 col-md-8">
        <?php echo isset($desa_id) ? getDesaName($desa_id, $desa_data) : ''; ?>
    </div>
</div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Alamat</div>
                                        <div class="col-lg-9 col-md-8"><?php echo $details_missing ? '' : $address; ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Nomor HP</div>
                                        <div class="col-lg-9 col-md-8"><?php echo $_SESSION['login_number']; ?></div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Email</div>
                                        <div class="col-lg-9 col-md-8"><?php echo $_SESSION['login_email']; ?></div>
                                    </div>
                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($message)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <form method="post">
                    <div class="row mb-3">
                        <label for="inputState" class="col-md-4 col-lg-3 col-form-label">Jenis Kelamin</label>
                        <div class="col-md-8 col-lg-9">
                            <select id="inputState" class="form-select" name="gender" required>
                                <option selected disabled value="">Pilih...</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            <div class="invalid-feedback">Pilih Jenis Kelamin Kamu!</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="birthDate" class="col-md-4 col-lg-3 col-form-label">Tanggal Lahir</label>
                        <div class="col-md-8 col-lg-9">
                            <input type="date" name="birth_date" class="form-control" id="birthDate" required>
                            <div class="invalid-feedback">Pilih Tanggal Lahir Kamu!</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                          <label for="province" class="col-md-4 col-lg-3 col-form-label">Provinsi</label>
                          <div class="col-md-8 col-lg-9">
                              <select id="province" class="form-select" name="province" required>
                                  <option selected disabled value="">Pilih Provinsi...</option>
                              </select>
                              <div class="invalid-feedback">Pilih Provinsi Anda!</div>
                          </div>
                      </div>

                      <div class="row mb-3">
                          <label for="city" class="col-md-4 col-lg-3 col-form-label">Kabupaten/Kota</label>
                          <div class="col-md-8 col-lg-9">
                              <select id="city" class="form-select" name="city" required>
                                  <option selected disabled value="">Pilih Kabupaten/Kota...</option>
                              </select>
                              <div class="invalid-feedback">Pilih Kabupaten/Kota Anda!</div>
                          </div>
                      </div>

                      <div class="row mb-3">
                          <label for="district" class="col-md-4 col-lg-3 col-form-label">Kecamatan</label>
                          <div class="col-md-8 col-lg-9">
                              <select id="district" class="form-select" name="district" required>
                                  <option selected disabled value="">Pilih Kecamatan...</option>
                              </select>
                              <div class="invalid-feedback">Pilih Kecamatan Anda!</div>
                          </div>
                      </div>

                      <div class="row mb-3">
                          <label for="subdistrict" class="col-md-4 col-lg-3 col-form-label">Kelurahan</label>
                          <div class="col-md-8 col-lg-9">
                              <select id="subdistrict" class="form-select" name="subdistrict" required>
                                  <option selected disabled value="">Pilih Kelurahan...</option>
                              </select>
                              <div class="invalid-feedback">Pilih Kelurahan Anda!</div>
                          </div>
                      </div>
                    <div class="row mb-3">
                        <label for="address" class="col-md-4 col-lg-3 col-form-label">Alamat</label>
                        <div class="col-md-8 col-lg-9">
                            <input name="address" type="text" class="form-control" id="address" value="Jalan Kencana">
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

                </div>

                
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->

  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>Klinik Pratama Anugrah Hexa</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">Artadevnymous</a>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
      $(document).ready(function () {
    // Load provinces from local JSON file
    $.getJSON('./data/provinsi.json', function (data) {
        var options = '<option selected disabled value="">Pilih Provinsi...</option>';
        $.each(data, function (index, value) {
            options += '<option value="' + value.id + '">' + value.name + '</option>';
        });
        $('#province').html(options);
    });

    // Load cities based on province selection
    $('#province').change(function () {
        var provinceId = $(this).val();
        if (provinceId) {
            $.getJSON('./data/kabupaten.json', function (data) {
                var options = '<option selected disabled value="">Pilih Kabupaten/Kota...</option>';
                $.each(data, function (index, value) {
                    if (value.idprovinsi == provinceId) {
                        options += '<option value="' + value.id + '">' + value.name + '</option>';
                    }
                });
                $('#city').html(options);
                $('#district').html('<option selected disabled value="">Pilih Kecamatan...</option>');
                $('#subdistrict').html('<option selected disabled value="">Pilih Kelurahan...</option>');
            });
        } else {
            $('#city').html('<option selected disabled value="">Pilih Kabupaten/Kota...</option>');
            $('#district').html('<option selected disabled value="">Pilih Kecamatan...</option>');
            $('#subdistrict').html('<option selected disabled value="">Pilih Kelurahan...</option>');
        }
    });

    // Load districts based on city selection
    $('#city').change(function () {
        var cityId = $(this).val();
        if (cityId) {
            $.getJSON('./data/kecamatan.json', function (data) {
                var options = '<option selected disabled value="">Pilih Kecamatan...</option>';
                $.each(data, function (index, value) {
                    if (value.idkabupaten == cityId) {
                        options += '<option value="' + value.id + '">' + value.name + '</option>';
                    }
                });
                $('#district').html(options);
                $('#subdistrict').html('<option selected disabled value="">Pilih Kelurahan...</option>');
            });
        } else {
            $('#district').html('<option selected disabled value="">Pilih Kecamatan...</option>');
            $('#subdistrict').html('<option selected disabled value="">Pilih Kelurahan...</option>');
        }
    });

    // Load subdistricts based on district selection
    $('#district').change(function () {
        var districtId = $(this).val();
        if (districtId) {
            $.getJSON('./data/desa.json', function (data) {
                var options = '<option selected disabled value="">Pilih Kelurahan...</option>';
                $.each(data, function (index, value) {
                    if (value.idkecamatan == districtId) {
                        options += '<option value="' + value.id + '">' + value.desa + '</option>';
                    }
                });
                $('#subdistrict').html(options);
            });
        } else {
            $('#subdistrict').html('<option selected disabled value="">Pilih Kelurahan...</option>');
        }
    });
});

    </script>
</body>

</html>