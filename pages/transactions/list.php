<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';
?>

<div class="p-6 md:p-8">
    <div class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="flex items-center justify-between px-7 py-6 border-b border-[#e2e8f0]">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#faf5ff] flex items-center justify-center text-[#7c3aed] text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h2 class="text-[1.1rem] font-bold text-[#1e293b]">Daftar Transaksi</h2>
                    <p class="text-[0.78rem] text-[#64748b] mt-0.5">Kelola seluruh riwayat transaksi penjualan
                        penjualan</p>
                </div>
            </div>
            <a href="/pages/transactions/add.php"
                class="inline-flex items-center gap-2 bg-[#7c3aed] hover:bg-[#6b21a8] text-white no-underline rounded-lg px-4 py-2.5 text-[0.85rem] font-semibold transition-colors duration-200 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                Tambah Transaksi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="border-b border-[#f1f5f9]">
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8] text-center">Kode / Order</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8]">Waktu Transaksi</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8]">Kasir</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8]">Metode Bayar</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8] text-right">Total Transaksi</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT t.*, k.nama_kasir, d.nama_donasi, d.harga_donasi
                            FROM transaksi t
                            LEFT JOIN kasir k ON t.id_kasir = k.id_kasir
                            LEFT JOIN donasi d ON t.id_donasi = d.id_donasi
                            ORDER BY t.tanggal DESC";
                    $result = mysqli_query($connection, $sql);

                    if (!$result || mysqli_num_rows($result) === 0) {
                        ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-[#64748b]">
                                <i class="fa-solid fa-receipt text-4xl text-[#cbd5e1] block mb-3"></i>
                                <p class="text-[0.9rem]">Belum ada data transaksi.
                                    <a href="/pages/transactions/add.php"
                                        class="text-[#7c3aed] font-semibold hover:underline">Tambah transaksi baru</a>.
                                </p>
                            </td>
                        </tr>
                        <?php
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $is_qris = $row['QRIS'] > 0;
                            ?>
                            <tr
                                class="border-b border-[#f8fafc] hover:bg-[#fafbff] transition-colors duration-150 last:border-none">
                                <td class="px-6 py-5 text-center font-semibold text-[#1e293b]">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="font-bold text-sm text-[#1e293b]"><?= htmlspecialchars($row["kode_transaksi"]) ?></span>
                                        <span
                                            class="inline-block mt-0.5 px-2 py-0.5 rounded text-[0.68rem] font-bold bg-gray-100 text-[#64748b]">
                                            Order #<?= htmlspecialchars($row["no_order"]) ?>
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-[#64748b]">
                                    <span
                                        class="block font-medium text-[#1e293b]"><?= date('d M Y', strtotime($row["tanggal"])) ?></span>
                                    <span class="text-xs text-black"><?= date('H:i:s', strtotime($row["tanggal"])) ?>
                                        WIB</span>
                                </td>

                                <td class="px-6 py-5 font-semibold text-[#1e293b]">
                                    <div>
                                        <span class="block"><?= htmlspecialchars($row["nama_kasir"]) ?></span>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    <?php if ($is_qris): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                            <i class="fa-solid fa-qrcode text-[0.7rem]"></i> QRIS
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-money-bill-wave text-[0.7rem]"></i> TUNAI
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-5 text-right font-bold text-[#1e293b]">
                                    <span class="text-base text-black">Rp
                                        <?= number_format($row["total"], 0, ',', '.') ?></span>
                                    <?php if (!empty($row["id_donasi"]) && !empty($row["harga_donasi"])): ?>
                                        <span class="block text-[0.7rem] font-normal text-[#059669]">
                                            +Donasi Rp <?= number_format($row["harga_donasi"], 0, ',', '.') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="/pages/transactions/transaction_details.php?kode_transaksi=<?= urlencode($row['kode_transaksi']) ?>"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-md border border-blue-300 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-[#003399] transition hover:bg-blue-100 no-underline"
                                            title="Lihat Detail Transaksi">
                                            <i class="fa-solid fa-eye text-[0.7rem]"></i>
                                            Detail
                                        </a>

                                        <a href="/pages/transactions/edit.php?kode_transaksi=<?= urlencode($row['kode_transaksi']) ?>"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-md border border-emerald-300 bg-emerald-100/50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100 no-underline"
                                            title="Edit Transaksi">
                                            <i class="fa-solid fa-pen-to-square text-[0.7rem]"></i>
                                            Edit
                                        </a>

                                        <button type="button"
                                            onclick="confirmDelete(event, '<?= $row['kode_transaksi'] ?>', 'Transaksi <?= $row['kode_transaksi'] ?> (#<?= $row['no_order'] ?>)')"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-md border border-red-300 bg-red-100/50 px-3 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-100 cursor-pointer"
                                            title="Hapus Transaksi">
                                            <i class="fa-solid fa-trash text-[0.7rem]"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    }

                    mysqli_close($connection);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$alertEntity = 'transaksi';
$alertDeleteUrl = '/process/transactions_process.php?action=delete&id=';
include ROOTPATH . '/includes/alert.php';
?>
</body>

</html>