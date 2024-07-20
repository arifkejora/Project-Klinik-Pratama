<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['login_doctor'])) {
    header("Location: pages-login-doctor.php");
    exit;
}

$id_antrian = isset($_GET['id_antrian']) ? $_GET['id_antrian'] : null;
if (!$id_antrian) {
    die("ID Antrian tidak ditemukan.");
}

function generateId($conn) {
    $sql = "SELECT id_resep FROM resep_obat ORDER BY id_resep DESC LIMIT 1";
    $result = $conn->query($sql);
  
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastId = $row['id_resep'];
        $lastNumber = intval(substr($lastId, 3)); // Get numbers after 'RSP'
        $newNumber = $lastNumber + 1;
        return 'RSP' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    } else {
        return 'RSP01'; // First ID if the table is empty
    }
}

$rekamMedisQuery = "SELECT id_rekam_medis, tekanan_darah_s, tekanan_darah_d, berat_badan, suhu_badan FROM rekam_medis WHERE id_antrian = ?";
$rekamMedisStmt = $conn->prepare($rekamMedisQuery);
$rekamMedisStmt->bind_param("i", $id_antrian);
$rekamMedisStmt->execute();
$rekamMedisResult = $rekamMedisStmt->get_result();

if ($rekamMedisResult->num_rows > 0) {
    $row = $rekamMedisResult->fetch_assoc();
    $id_rekammedis = $row['id_rekam_medis'];
    $tekanan_darah_s = $row['tekanan_darah_s'];
    $tekanan_darah_d = $row['tekanan_darah_d'];
    $berat_badan = $row['berat_badan'];
    $suhu_badan = $row['suhu_badan'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $keluhan = $_POST['keluhan'];
        $diagnosa = $_POST['diagnosa'];
        $tekanan_darah_s = $_POST['tekanan_darah_s'];
        $tekanan_darah_d = $_POST['tekanan_darah_d'];
        $berat_badan = $_POST['berat_badan'];
        $suhu_badan = $_POST['suhu_badan'];
        $hasil_pemeriksaan = $_POST['hasil_pemeriksaan'];
        $resep_obat_ids = isset($_POST['resep_obat_ids']) ? explode(',', $_POST['resep_obat_ids']) : [];
        $newId = generateId($conn);

        // Update rekam medis
        $updateQuery = "
            UPDATE rekam_medis 
            SET keluhan = ?, diagnosa = ?, tekanan_darah_s = ?, tekanan_darah_d = ?, berat_badan = ?, suhu_badan = ?, hasil_pemeriksaan = ?, status_pembayaran = 'belum lunas'
            WHERE id_antrian = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssssssi", $keluhan, $diagnosa, $tekanan_darah_s, $tekanan_darah_d, $berat_badan, $suhu_badan, $hasil_pemeriksaan, $id_antrian);
        if (!$updateStmt->execute()) {
            error_log("Error updating rekam medis: " . $updateStmt->error);
            exit;
        }
        $updateStmt->close();

        // Prepare statements for inserting prescriptions and updating stock
        $resepObatQuery = "
            INSERT INTO resep_obat (id_resep, id_rekammedis, id_obat, status) 
            VALUES (?, ?, ?, 'Antrian')";
        $updateStokQuery = "
            UPDATE obat
            SET stok = stok - 1
            WHERE id_obat = ? AND stok > 0";

        $resepObatStmt = $conn->prepare($resepObatQuery);
        $updateStokStmt = $conn->prepare($updateStokQuery);

        if (!$resepObatStmt || !$updateStokStmt) {
            error_log("Error preparing statements: " . $conn->error);
            exit;
        }

        // Ensure proper handling of SQL errors
        $conn->autocommit(FALSE);
        try {
            foreach ($resep_obat_ids as $id_obat) {
                $id_obat = (int)trim($id_obat); // Ensure id_obat is an integer
                if ($id_obat > 0) {
                    // Insert into resep_obat
                    $resepObatStmt->bind_param("iii", $newId, $id_rekammedis, $id_obat);
                    if (!$resepObatStmt->execute()) {
                        throw new Exception("Error inserting into resep_obat: " . $resepObatStmt->error);
                    }

                    // Update stock
                    $updateStokStmt->bind_param("i", $id_obat);
                    if (!$updateStokStmt->execute()) {
                        throw new Exception("Error updating stock: " . $updateStokStmt->error);
                    }
                }
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log($e->getMessage());
            exit;
        }

        $resepObatStmt->close();
        $updateStokStmt->close();

        // Update antrian status
        $updateAntrianQuery = "
            UPDATE antrian 
            SET status_antrian = 'Selesai Diperiksa' 
            WHERE id_antrian = ?";
        $updateAntrianStmt = $conn->prepare($updateAntrianQuery);
        $updateAntrianStmt->bind_param("i", $id_antrian);
        if (!$updateAntrianStmt->execute()) {
            error_log("Error updating antrian: " . $updateAntrianStmt->error);
            exit;
        }
        $updateAntrianStmt->close();

        header("Location: doctor-patient.php");
        exit;
    }
} else {
    die("Rekam medis untuk ID Antrian tidak ditemukan.");
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Periksa Pasien - Dokter</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Periksa Pasien</h4>
            </div>
            <div class="card-body">
                <form class="row g-3" action="" method="POST">
                    <div class="col-md-12">
                        <label for="inputKeluhan" class="form-label">Keluhan</label>
                        <textarea class="form-control" id="inputKeluhan" name="keluhan" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputDiagnosa" class="form-label">Diagnosa</label>
                        <textarea class="form-control" id="inputDiagnosa" name="diagnosa" rows="3" required></textarea>
                    </div>
                    <div class="col-md-2">
                        <label for="inputTekananDarahS" class="form-label">Tekanan Darah_S</label>
                        <input type="text" class="form-control" id="inputTekananDarahS" name="tekanan_darah_s" value="<?php echo htmlspecialchars($tekanan_darah_s); ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label for="inputTekananDarahD" class="form-label">Tekanan Darah_D</label>
                        <input type="text" class="form-control" id="inputTekananDarahD" name="tekanan_darah_d" value="<?php echo htmlspecialchars($tekanan_darah_d); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputBeratBadan" class="form-label">Berat Badan</label>
                        <input type="text" class="form-control" id="inputBeratBadan" name="berat_badan" value="<?php echo htmlspecialchars($berat_badan); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="inputSuhuBadan" class="form-label">Suhu Badan</label>
                        <input type="text" class="form-control" id="inputSuhuBadan" name="suhu_badan" value="<?php echo htmlspecialchars($suhu_badan); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label for="inputHasilPemeriksaan" class="form-label">Hasil Pemeriksaan</label>
                        <textarea class="form-control" id="inputHasilPemeriksaan" name="hasil_pemeriksaan" rows="3" required></textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="inputResep" class="form-label">Resep Obat</label>
                        <input type="text" class="form-control" id="inputResep" name="resep_obat" placeholder="Ketikan nama obat..." required>
                        <input type="hidden" id="inputResepIds" name="resep_obat_ids">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(function() {
        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        $("#inputResep").on("keydown", function(event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).autocomplete("instance").menu.active) {
                event.preventDefault();
            }
        }).autocomplete({
            source: function(request, response) {
                $.getJSON("get-obat.php", {
                    term: extractLast(request.term)
                }, response);
            },
            search: function() {
                var term = extractLast(this.value);
                if (term.length < 2) {
                    return false;
                }
            },
            focus: function() {
                return false;
            },
            select: function(event, ui) {
                var terms = split(this.value);
                var termsIds = $("#inputResepIds").val().split(/,\s*/);

                terms.pop();
                terms.push(ui.item.value); 
                terms.push("");

                termsIds.pop();
                termsIds.push(ui.item.id);
                termsIds.push("");

                this.value = terms.join(", ");
                $("#inputResepIds").val(termsIds.join(", "));

                return false;
            }
        });

        $(function() {
            $("form").submit(function(event) {
                var inputResepIds = $("#inputResepIds");
                console.log('inputResepIds:', inputResepIds); // Check if the element is found
                console.log('inputResepIds value before filter:', inputResepIds.val()); // Check the value before processing

                if (inputResepIds.length) {
                    var termsIds = inputResepIds.val().split(/,\s*/);
                    inputResepIds.val(termsIds.filter(id => id !== ""));
                    console.log('inputResepIds value after filter:', inputResepIds.val()); // Check the value after processing
                } else {
                    console.error('Element #inputResepIds not found.');
                }

                return true; // Ensure the form submission proceeds
            });
        });


    });
    </script>
</body>
</html>