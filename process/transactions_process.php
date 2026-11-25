<?php
include("../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == "add") {
        $kode_transaksi = !empty($_POST['kode_transaksi']) ? $_POST['kode_transaksi'] : 'TIKL-' . rand(1000000, 9999999);
        $tanggal = !empty($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d H:i:s');
        $id_kasir = !empty($_POST['id_kasir']) ? $_POST['id_kasir'] : 1;

        if (!empty($_POST['no_order'])) {
            $no_order = $_POST['no_order'];
        } else {
            $no_order = 'A01';
            $q_order = mysqli_query($connection, "SELECT no_order FROM transaksi ORDER BY LENGTH(no_order) DESC, no_order DESC LIMIT 1");
            if ($q_order && mysqli_num_rows($q_order) > 0) {
                $last_order = strtoupper(trim(mysqli_fetch_assoc($q_order)['no_order']));
                if (preg_match('/^([A-Z]+)(\d+)$/', $last_order, $m)) {
                    $letter = $m[1];
                    $num = (int) $m[2] + 1;
                    if ($num > 99) {
                        $letter++;
                        $num = 1;
                    }
                    $no_order = $letter . str_pad($num, 2, '0', STR_PAD_LEFT);
                }
            }
        }

        $id_donasi_raw = !empty($_POST['id_donasi']) ? $_POST['id_donasi'] : null;

        $donasi_val = 0;
        if ($id_donasi_raw !== null) {
            $d_res = mysqli_query($connection, "SELECT harga_donasi FROM donasi WHERE id_donasi = '" . mysqli_real_escape_string($connection, $id_donasi_raw) . "'");
            if ($d_res && $d_row = mysqli_fetch_assoc($d_res)) {
                $donasi_val = (int) $d_row['harga_donasi'];
            }
        }

        $item_sum = 0;
        $detail_rows = [];
        if (isset($_POST['id_produk']) && is_array($_POST['id_produk'])) {
            foreach ($_POST['id_produk'] as $index => $id_produk) {
                if (!empty($id_produk)) {
                    $harga = (int) ($_POST['harga'][$index] ?? 0);
                    $kuantitas = (int) ($_POST['kuantitas'][$index] ?? 1);
                    $diskon = (int) ($_POST['diskon'][$index] ?? 0);
                    $harga_jual = ($harga - $diskon) * $kuantitas;
                    $item_sum += $harga_jual;
                    $detail_rows[] = compact('id_produk', 'harga', 'kuantitas', 'diskon', 'harga_jual');
                }
            }
        }

        $total = $item_sum + $donasi_val;

        $payment_method = $_POST['payment_method'] ?? 'tunai';
        if ($payment_method === 'qris') {
            $QRIS = $total;
            $tunai = 0;
            $kembalian = 0;
        } else {
            $QRIS = 0;
            $tunai = (int) (isset($_POST['tunai']) && $_POST['tunai'] !== '' ? $_POST['tunai'] : 0);
            $kembalian = ($tunai > $total) ? ($tunai - $total) : 0;
        }

        $id_donasi_sql = ($id_donasi_raw !== null) ? "'$id_donasi_raw'" : "NULL";

        $sql = "INSERT INTO transaksi (kode_transaksi, tanggal, id_kasir, no_order, QRIS, total, tunai, kembalian, id_donasi) 
                VALUES ('$kode_transaksi', '$tanggal', '$id_kasir', '$no_order', '$QRIS', '$total', '$tunai', '$kembalian', $id_donasi_sql)";

        try {
            if (mysqli_query($connection, $sql)) {
                foreach ($detail_rows as $row) {
                    mysqli_query($connection, "INSERT INTO detail_transaksi (kode_transaksi, id_produk, harga, kuantitas, diskon, harga_jual) 
                                               VALUES ('$kode_transaksi', '{$row['id_produk']}', '{$row['harga']}', '{$row['kuantitas']}', '{$row['diskon']}', '{$row['harga_jual']}')");
                }

                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=success_add");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/transactions/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $error = urlencode("Duplicate entry for transaction code.");
            } else {
                $error = urlencode($e->getMessage());
            }
            header("Location: /pages/transactions/list.php?status=error&message=$error");
            exit();
        }
    } else if ($action == "edit") {
        $kode_transaksi = $_POST['kode_transaksi'];
        $id_kasir = $_POST['id_kasir'];
        $no_order = $_POST['no_order'];
        $id_donasi = !empty($_POST['id_donasi']) ? "'" . $_POST['id_donasi'] . "'" : "NULL";

        $donasi_val = 0;
        if ($id_donasi !== "NULL") {
            $d_res = mysqli_query($connection, "SELECT harga_donasi FROM donasi WHERE id_donasi = $id_donasi");
            if ($d_res && $d_row = mysqli_fetch_assoc($d_res)) {
                $donasi_val = $d_row['harga_donasi'];
            }
        }

        $item_sum = 0;
        try {
            mysqli_query($connection, "DELETE FROM detail_transaksi WHERE kode_transaksi = '$kode_transaksi'");

            if (isset($_POST['id_produk']) && is_array($_POST['id_produk'])) {
                foreach ($_POST['id_produk'] as $index => $id_produk) {
                    if (!empty($id_produk)) {
                        $harga = $_POST['harga'][$index] ?? 0;
                        $kuantitas = $_POST['kuantitas'][$index] ?? 1;
                        $diskon = $_POST['diskon'][$index] ?? 0;
                        $harga_jual = ($harga - $diskon) * $kuantitas;
                        $item_sum += $harga_jual;

                        mysqli_query($connection, "INSERT INTO detail_transaksi (kode_transaksi, id_produk, harga, kuantitas, diskon, harga_jual) 
                                                   VALUES ('$kode_transaksi', '$id_produk', '$harga', '$kuantitas', '$diskon', '$harga_jual')");
                    }
                }
            }

            $total = $item_sum + $donasi_val;
            $payment_method = $_POST['payment_method'] ?? (($_POST['QRIS'] ?? 0) > 0 ? 'qris' : 'tunai');

            if ($payment_method === 'qris') {
                $QRIS = $total;
                $tunai = 0;
                $kembalian = 0;
            } else {
                $QRIS = 0;
                $tunai = isset($_POST['tunai']) && $_POST['tunai'] !== '' ? $_POST['tunai'] : 0;
                $kembalian = ($tunai > $total) ? ($tunai - $total) : 0;
            }

            $sql = "UPDATE transaksi SET 
                    id_kasir = '$id_kasir', 
                    no_order = '$no_order', 
                    QRIS = '$QRIS', 
                    total = '$total', 
                    tunai = '$tunai', 
                    kembalian = '$kembalian', 
                    id_donasi = $id_donasi 
                    WHERE kode_transaksi = '$kode_transaksi'";

            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=" . urlencode($kode_transaksi) . "&status=success_edit");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=" . urlencode($kode_transaksi) . "&status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $error = urlencode($e->getMessage());
            header("Location: /pages/transactions/transaction_details.php?kode_transaksi=" . urlencode($kode_transaksi) . "&status=error&message=$error");
            exit();
        }
    } else if ($action == "add_detail") {
        $kode_transaksi = $_POST['kode_transaksi'];
        $id_produk = $_POST['id_produk'];
        $harga = $_POST['harga'];
        $kuantitas = $_POST['kuantitas'];
        $diskon = isset($_POST['diskon']) && $_POST['diskon'] !== '' ? $_POST['diskon'] : 0;
        $harga_jual = ($harga - $diskon) * $kuantitas;

        $sql = "INSERT INTO detail_transaksi (kode_transaksi, id_produk, harga, kuantitas, diskon, harga_jual) 
                VALUES ('$kode_transaksi', '$id_produk', '$harga', '$kuantitas', '$diskon', '$harga_jual')";

        try {
            if (mysqli_query($connection, $sql)) {
                $calc_items = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COALESCE(SUM(harga_jual), 0) as total_items FROM detail_transaksi WHERE kode_transaksi = '$kode_transaksi'"))['total_items'] ?? 0;
                $tx_info = mysqli_fetch_assoc(mysqli_query($connection, "SELECT t.QRIS, t.tunai, COALESCE(d.harga_donasi, 0) as donasi_val FROM transaksi t LEFT JOIN donasi d ON t.id_donasi = d.id_donasi WHERE t.kode_transaksi = '$kode_transaksi'"));
                $new_total = $calc_items + ($tx_info['donasi_val'] ?? 0);

                if (($tx_info['QRIS'] ?? 0) > 0) {
                    mysqli_query($connection, "UPDATE transaksi SET total = '$new_total', QRIS = '$new_total', tunai = 0, kembalian = 0 WHERE kode_transaksi = '$kode_transaksi'");
                } else {
                    $new_kembalian = (($tx_info['tunai'] ?? 0) > $new_total) ? ($tx_info['tunai'] - $new_total) : 0;
                    mysqli_query($connection, "UPDATE transaksi SET total = '$new_total', kembalian = '$new_kembalian' WHERE kode_transaksi = '$kode_transaksi'");
                }

                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=success_add");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $error = urlencode($e->getMessage());
            header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=error&message=$error");
            exit();
        }
    }

    mysqli_close($connection);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == "delete") {
        $kode_transaksi = $_GET['id'] ?? $_GET['kode_transaksi'] ?? '';

        try {
            mysqli_query($connection, "DELETE FROM detail_transaksi WHERE kode_transaksi = '$kode_transaksi'");

            $sql = "DELETE FROM transaksi WHERE kode_transaksi = '$kode_transaksi'";
            if (mysqli_query($connection, $sql)) {
                header("Location: /pages/transactions/list.php?status=success_delete");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/transactions/list.php?status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $error = urlencode($e->getMessage());
            header("Location: /pages/transactions/list.php?status=error&message=$error");
            exit();
        }
    } else if ($action == "delete_detail") {
        $id_detail = $_GET['id_detail'];
        $kode_transaksi = $_GET['kode_transaksi'];

        try {
            $sql = "DELETE FROM detail_transaksi WHERE id_detail = '$id_detail'";
            if (mysqli_query($connection, $sql)) {
                $calc_items = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COALESCE(SUM(harga_jual), 0) as total_items FROM detail_transaksi WHERE kode_transaksi = '$kode_transaksi'"))['total_items'] ?? 0;
                $tx_info = mysqli_fetch_assoc(mysqli_query($connection, "SELECT t.QRIS, t.tunai, COALESCE(d.harga_donasi, 0) as donasi_val FROM transaksi t LEFT JOIN donasi d ON t.id_donasi = d.id_donasi WHERE t.kode_transaksi = '$kode_transaksi'"));
                $new_total = $calc_items + ($tx_info['donasi_val'] ?? 0);

                if (($tx_info['QRIS'] ?? 0) > 0) {
                    mysqli_query($connection, "UPDATE transaksi SET total = '$new_total', QRIS = '$new_total', tunai = 0, kembalian = 0 WHERE kode_transaksi = '$kode_transaksi'");
                } else {
                    $new_kembalian = (($tx_info['tunai'] ?? 0) > $new_total) ? ($tx_info['tunai'] - $new_total) : 0;
                    mysqli_query($connection, "UPDATE transaksi SET total = '$new_total', kembalian = '$new_kembalian' WHERE kode_transaksi = '$kode_transaksi'");
                }

                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=success_delete");
                exit();
            } else {
                $error = urlencode(mysqli_error($connection));
                header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=error&message=$error");
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            $error = urlencode($e->getMessage());
            header("Location: /pages/transactions/transaction_details.php?kode_transaksi=$kode_transaksi&status=error&message=$error");
            exit();
        }
    }

    mysqli_close($connection);
}
?>