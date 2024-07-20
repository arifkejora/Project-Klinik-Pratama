<?php
session_start();
include('db_connection.php'); // Ensure this file contains the correct DB connection details

// Function to generate the ID
function generateId($conn) {
    $sql = "SELECT id_pasien FROM pasien ORDER BY id_pasien DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['id_pasien'];
        $lastNumber = intval(substr($lastId, 2));
        $newNumber = $lastNumber + 1;
        return 'PS' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    } else {
        return 'PS01'; // First ID if the table is empty
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $password = $_POST['password'];

    // To protect against MySQL injection
    $name = stripslashes($name);
    $email = stripslashes($email);
    $number = stripslashes($number);
    $password = stripslashes($password);

    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $number = mysqli_real_escape_string($conn, $number);
    $password = mysqli_real_escape_string($conn, $password);

    // Hashing the password with MD5
    $hashed_password = md5($password);

    // Generate new ID
    $newId = generateId($conn);

    // Insert into the database
    $sql = "INSERT INTO pasien (id_pasien, nama_pasien, email_pasien, nomorhp_pasien, katasandi_pasien) VALUES ('$newId', '$name', '$email', '$number', '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        header("location: pages-login-patient.php"); // Redirect to login page
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register - NiceAdmin Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">


            <div class="d-flex justify-content-center py-4">
                                <a href="index.html" class="logo d-flex align-items-center w-auto">
                                    <span class="d-none d-lg-block">Pratama Anugrah Hexa</span>
                                </a>
                            </div>

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Registrasi Akun</h5>
                    <p class="text-center small">Masukkan data kamu dengan benar</p>
                  </div>

                  <form class="row g-3 needs-validation" method="post" action="" novalidate>
                                        <div class="col-12">
                                            <label for="yourName" class="form-label">Nama Lengkap</label>
                                            <input type="text" name="name" class="form-control" id="yourName" required>
                                            <div class="invalid-feedback">Please, enter your name!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" id="yourEmail" required>
                                            <div class="invalid-feedback">Please enter a valid Email address!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourNumber" class="form-label">Nomor HP</label>
                                            <input type="text" name="number" class="form-control" id="yourNumber" required>
                                            <div class="invalid-feedback">Please enter a valid Phone Number!</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Kata Sandi</label>
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                            <div class="invalid-feedback">Please enter your password!</div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Buat Akun</button>
                                        </div>
                                        <div class="col-12">
                                            <p class="small mb-0">Sudah Punya Akun? <a href="pages-login-patient.php">Masuk</a></p>
                                        </div>
                                    </form>

                                    <?php if (isset($error)) { ?>
                                        <div class="alert alert-danger mt-3" role="alert">
                                            <?php echo $error; ?>
                                        </div>
                                    <?php } ?>

                </div>
              </div>



            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

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

</body>

</html>