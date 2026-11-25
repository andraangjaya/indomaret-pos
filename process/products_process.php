<?php
include("../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $nama_produk = $_POST['nama_produk'];
        $harga = $_POST['harga'];
        $diskon = isset($_POST['diskon']) && $_POST['diskon'] !== '' ? $_POST['diskon'] : 0;

        $sql = "INSERT INTO produk (nama_produk, harga, diskon) VALUES ('$nama_produk', '$harga', '$diskon')";

        try {
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/products/list.php?status=success_add");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/products/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for product.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/products/list.php?status=error&message=$error");
            exit();
        }
    } else if ($action == "edit") {
        $id_produk = $_POST['id_produk'];
        $nama_produk = $_POST['nama_produk'];
        $harga = $_POST['harga'];
        $diskon = isset($_POST['diskon']) && $_POST['diskon'] !== '' ? $_POST['diskon'] : 0;

        $sql = "UPDATE produk SET nama_produk = '$nama_produk', harga = '$harga', diskon = '$diskon' WHERE id_produk = '$id_produk'";

        try {
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/products/list.php?status=success_edit");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/products/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for product.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/products/list.php?status=error&message=$error");
            exit();
        }
    }

    mysqli_close($connection);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == "delete") {
    $id_produk = $_GET['id'];

    $sql = "DELETE FROM produk WHERE id_produk = '$id_produk'";

    try {
        if (mysqli_query($connection, $sql)) {
            header("Location: /pages/products/list.php?status=success_delete");
            exit();
        } else {
            $error = urlencode(mysqli_error($connection));
            header("Location: /pages/products/list.php?status=error&message=$error");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        $error = urlencode($e->getMessage());
        header("Location: /pages/products/list.php?status=error&message=$error");
        exit();
    }

    mysqli_close($connection);
}
?>
