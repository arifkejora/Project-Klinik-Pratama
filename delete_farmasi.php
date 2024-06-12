<?php
session_start();
include('db_connection.php');

if (isset($_GET['id_farmasi'])) {
    $id = $_GET['id_farmasi'];
    $sql = "DELETE FROM farmasi WHERE id_farmasi = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin_farmasi.php");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_farmasi.php");
}
?>
