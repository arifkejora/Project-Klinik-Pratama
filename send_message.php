<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $token = 'UDwdcnrNsLqz7B@Ak77A'; // Ganti dengan token Anda

    // Ambil nomor HP dari database
    include('db_connection.php'); // Pastikan file ini sudah benar

    // Query untuk mengambil nomor HP dari tabel pasien
    $sql = "SELECT nomorhp_pasien FROM pasien";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Memproses setiap nomor HP
        while ($row = mysqli_fetch_assoc($result)) {
            $target = $row['nomorhp_pasien'];

            // Mengirim pesan menggunakan API
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62', // Kode negara opsional (Indonesia)
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            echo "Pesan dikirim ke: " . $target . "<br>";
            echo "Response: " . $response . "<br><br>";
        }
    } else {
        echo "Tidak ada nomor HP yang ditemukan dalam database.";
    }

    mysqli_close($conn);

    // Redirect ke admin_crm.php setelah 5 detik
    echo '<meta http-equiv="refresh" content="5;url=admin_crm.php">';
    echo 'Redirecting to admin_crm.php in 5 seconds...';
    exit;
} else {
    echo 'Invalid request method.';
}
?>
