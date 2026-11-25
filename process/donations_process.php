<?php
include("../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $nama_donasi = $_POST['nama_donasi'];
        $harga_donasi = $_POST['harga_donasi'];

        $sql = "INSERT INTO donasi (nama_donasi, harga_donasi) VALUES ('$nama_donasi', '$harga_donasi')";

        try {
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/donations/list.php?status=success_add");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/donations/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for donation.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/donations/list.php?status=error&message=$error");
            exit();
        }
    } else if ($action == "edit") {
        $id_donasi = $_POST['id_donasi'];
        $nama_donasi = $_POST['nama_donasi'];
        $harga_donasi = $_POST['harga_donasi'];

        $sql = "UPDATE donasi SET nama_donasi = '$nama_donasi', harga_donasi = '$harga_donasi' WHERE id_donasi = '$id_donasi'";

        try {
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/donations/list.php?status=success_edit");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/donations/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for donation.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/donations/list.php?status=error&message=$error");
            exit();
        }
    }

    mysqli_close($connection);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == "delete") {
    $id_donasi = $_GET['id'];

    $sql = "DELETE FROM donasi WHERE id_donasi = '$id_donasi'";

    try {
        if (mysqli_query($connection, $sql)) {
            header("Location: /pages/donations/list.php?status=success_delete");
            exit();
        } else {
            $error = urlencode(mysqli_error($connection));
            header("Location: /pages/donations/list.php?status=error&message=$error");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        $error = urlencode($e->getMessage());
        header("Location: /pages/donations/list.php?status=error&message=$error");
        exit();
    }

    mysqli_close($connection);
}
?>
