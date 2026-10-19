<?php
define('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . '/');
include ROOTPATH . '/includes/header.php';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indomaret POS - Tambah Kasir</title>
    <!-- Link to Separate CSS File -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- FontAwesome for POS Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="page-wrapper">
        <div class="pos-card">
            <div class="card-header">
                <i class="fa-solid fa-user-plus"></i>
                <div>
                    <h2>Tambah Kasir Baru</h2>
                    <p>Masukkan informasi data kasir terminal</p>
                </div>
            </div>

            <form action="/process/cashiers_process.php" method="post" class="pos-form">
                <input type="text" name="nama_kasir" placeholder="Nama Kasir" required autocomplete="off">
                <input type="text" name="no_telp" placeholder="Nomor Telepon" required autocomplete="off">
                <input type="text" name="alamat" placeholder="Alamat" required autocomplete="off">

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-check"></i> Simpan Kasir
                </button>
            </form>
        </div>
    </div>
</body>

</html>