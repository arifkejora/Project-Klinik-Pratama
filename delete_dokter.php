<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_dokter'])) {
    $id = $_GET['id_dokter'];

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Step 1: Delete related records in rating that reference rekam_medis
        $sql_delete_rating = "DELETE FROM rating WHERE id_rekam_medis IN (SELECT id_rekam_medis FROM rekam_medis WHERE id_antrian IN (SELECT id_antrian FROM antrian WHERE id_jadwal IN (SELECT id_jadwal FROM jadwal_dokter WHERE id_dokter = $id)))";
        if (!mysqli_query($conn, $sql_delete_rating)) {
            throw new Exception("Error deleting related records in rating: " . mysqli_error($conn));
        }

        // Step 2: Delete related records in rujukan that reference rekam_medis
        $sql_delete_rujukan = "DELETE FROM rujukan WHERE id_rekammedis IN (SELECT id_rekam_medis FROM rekam_medis WHERE id_antrian IN (SELECT id_antrian FROM antrian WHERE id_jadwal IN (SELECT id_jadwal FROM jadwal_dokter WHERE id_dokter = $id)))";
        if (!mysqli_query($conn, $sql_delete_rujukan)) {
            throw new Exception("Error deleting related records in rujukan: " . mysqli_error($conn));
        }

        // Step 3: Delete related records in resep_obat that reference rekam_medis
        $sql_delete_resep_obat = "DELETE FROM resep_obat WHERE id_rekammedis IN (SELECT id_rekam_medis FROM rekam_medis WHERE id_antrian IN (SELECT id_antrian FROM antrian WHERE id_jadwal IN (SELECT id_jadwal FROM jadwal_dokter WHERE id_dokter = $id)))";
        if (!mysqli_query($conn, $sql_delete_resep_obat)) {
            throw new Exception("Error deleting related records in resep_obat: " . mysqli_error($conn));
        }

        // Step 4: Delete related records in rekam_medis that reference antrian
        $sql_delete_rekam_medis = "DELETE FROM rekam_medis WHERE id_antrian IN (SELECT id_antrian FROM antrian WHERE id_jadwal IN (SELECT id_jadwal FROM jadwal_dokter WHERE id_dokter = $id))";
        if (!mysqli_query($conn, $sql_delete_rekam_medis)) {
            throw new Exception("Error deleting related records in rekam_medis: " . mysqli_error($conn));
        }

        // Step 5: Delete related records in antrian that reference jadwal_dokter
        $sql_delete_antrian = "DELETE FROM antrian WHERE id_jadwal IN (SELECT id_jadwal FROM jadwal_dokter WHERE id_dokter = $id)";
        if (!mysqli_query($conn, $sql_delete_antrian)) {
            throw new Exception("Error deleting related records in antrian: " . mysqli_error($conn));
        }

        // Step 6: Delete related records in jadwal_dokter
        $sql_delete_schedule = "DELETE FROM jadwal_dokter WHERE id_dokter = $id";
        if (!mysqli_query($conn, $sql_delete_schedule)) {
            throw new Exception("Error deleting related records in jadwal_dokter: " . mysqli_error($conn));
        }

        // Step 7: Delete the doctor record
        $sql_delete_doctor = "DELETE FROM dokter WHERE id_dokter = $id";
        if (!mysqli_query($conn, $sql_delete_doctor)) {
            throw new Exception("Error deleting doctor record: " . mysqli_error($conn));
        }

        // Commit the transaction
        mysqli_commit($conn);
        header("Location: admin_dokter.php");
    } catch (Exception $e) {
        // Rollback the transaction
        mysqli_rollback($conn);
        echo $e->getMessage();
    }
} else {
    header("Location: admin_dokter.php");
}
?>
