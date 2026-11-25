<?php
include("../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $nama_kasir = $_POST['nama_kasir'];
        $no_telp = $_POST['no_telp'];
        $alamat = $_POST['alamat'];

        $sql = "INSERT INTO kasir (nama_kasir, no_telp, alamat) VALUES ('$nama_kasir', '$no_telp', '$alamat')";

        try {
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/cashiers/list.php?status=success_add");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/cashiers/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for cashier.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/cashiers/list.php?status=error&message=$error");
            exit();
        }
    } else if ($action == "edit") {
        $id_kasir = $_POST['id_kasir'];
        $nama_kasir = $_POST['nama_kasir'];
        $no_telp = $_POST['no_telp'];
        $alamat = $_POST['alamat'];

        $sql = "UPDATE kasir SET nama_kasir = '$nama_kasir', no_telp = '$no_telp', alamat = '$alamat' WHERE id_kasir = '$id_kasir'";

        try {
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/cashiers/list.php?status=success_edit");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/cashiers/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for cashier.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/cashiers/list.php?status=error&message=$error");
            exit();
        }
    }

    mysqli_close($connection);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == "delete") {
    $id_kasir = $_GET['id'];

    $sql = "DELETE FROM kasir WHERE id_kasir = '$id_kasir'";

    try {
        if (mysqli_query($connection, $sql)) {
            header("Location: /pages/cashiers/list.php?status=success_delete");
            exit();
        } else {
            $error = urlencode(mysqli_error($connection));
            header("Location: /pages/cashiers/list.php?status=error&message=$error");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        $error = urlencode($e->getMessage());
        header("Location: /pages/cashiers/list.php?status=error&message=$error");
        exit();
    }

    mysqli_close($connection);
}
?>
