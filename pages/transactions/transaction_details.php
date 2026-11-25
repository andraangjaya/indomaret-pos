<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

$kode_transaksi = $_GET['kode_transaksi'] ?? $_GET['id'] ?? '';

if (empty($kode_transaksi)) {
    $get_latest = mysqli_query($connection, "SELECT kode_transaksi FROM transaksi ORDER BY tanggal DESC LIMIT 1");
    if ($get_latest && mysqli_num_rows($get_latest) > 0) {
        $kode_transaksi = mysqli_fetch_assoc($get_latest)['kode_transaksi'];
    }
}

$sql_tx = "SELECT t.*, k.nama_kasir, k.no_telp as no_telp_kasir, d.nama_donasi, d.harga_donasi
           FROM transaksi t 
           LEFT JOIN kasir k ON t.id_kasir = k.id_kasir 
           LEFT JOIN donasi d ON t.id_donasi = d.id_donasi 
           WHERE t.kode_transaksi = '$kode_transaksi'";
$res_tx = mysqli_query($connection, $sql_tx);
$tx = ($res_tx && mysqli_num_rows($res_tx) > 0) ? mysqli_fetch_assoc($res_tx) : null;

$items = [];
$subtotal_items = 0;
$total_item_qty = 0;

if ($tx) {
    $sql_items = "SELECT dt.*, p.nama_produk 
                  FROM detail_transaksi dt 
                  LEFT JOIN produk p ON dt.id_produk = p.id_produk 
                  WHERE dt.kode_transaksi = '$kode_transaksi' 
                  ORDER BY dt.id_detail ASC";
    $res_items = mysqli_query($connection, $sql_items);
    if ($res_items) {
        while ($row = mysqli_fetch_assoc($res_items)) {
            $items[] = $row;
            $subtotal_items += $row['harga_jual'];
            $total_item_qty += $row['kuantitas'];
        }
    }
}
?>

<div class="p-6 md:p-8 max-w-[1400px] mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div class="flex items-center gap-3">
            <a href="/pages/transactions/list.php"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-[#e2e8f0] text-[#64748b] hover:text-[#003399] hover:border-[#003399] transition shadow-sm">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-extrabold text-[#1e293b] tracking-tight">Detail Transaksi</h1>
                </div>
            </div>
        </div>

        <?php if ($tx): ?>
            <div class="flex items-center gap-2.5">
                <a href="/pages/transactions/edit.php?kode_transaksi=<?= urlencode($tx['kode_transaksi']) ?>"
                    class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 rounded-xl px-4 py-2.5 text-xs md:text-sm font-semibold transition shadow-sm no-underline">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    <span>Edit</span>
                </a>
                <button type="button"
                    onclick="confirmDelete(event, '<?= $tx['kode_transaksi'] ?>', 'Transaksi <?= $tx['kode_transaksi'] ?> (#<?= $tx['no_order'] ?>)')"
                    class="inline-flex items-center gap-2 bg-red-50 border border-red-200 hover:bg-red-100 text-red-600 rounded-xl px-4 py-2.5 text-xs md:text-sm font-semibold transition shadow-sm cursor-pointer">
                    <i class="fa-solid fa-trash text-xs"></i>
                    <span>Hapus Transaksi</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$tx): ?>
        <div class="bg-white rounded-2xl border border-[#e2e8f0] p-12 text-center text-[#64748b] shadow-sm">
            <i class="fa-solid fa-receipt text-5xl text-[#cbd5e1] block mb-4"></i>
            <h3 class="text-lg font-bold text-[#1e293b] mb-1">Transaksi Tidak Ditemukan</h3>
            <p class="text-sm max-w-md mx-auto mb-6">Kode transaksi yang Anda cari tidak tersedia atau belum ada data
                transaksi yang tersimpan di sistem.</p>
            <a href="/pages/transactions/list.php"
                class="inline-flex items-center gap-2 bg-[#003399] hover:bg-[#002277] text-white rounded-xl px-5 py-2.5 text-sm font-semibold transition no-underline">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Daftar Transaksi</span>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-7 py-5 border-b border-[#e2e8f0] flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                            <div>
                                <h2 class="text-base font-bold text-[#1e293b]">Informasi Transaksi</h2>
                                <p class="text-xs font-bold text-black">
                                    <?= htmlspecialchars($tx['kode_transaksi']) ?>
                                </p>
                            </div>
                        </div>
                        <div>
                            <?php if ($tx['QRIS'] > 0): ?>
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
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-[#e2e8f0] border-b border-[#e2e8f0] bg-[#f8fafc] text-xs">
                        <div class="p-4">
                            <span class="text-[#64748b] block font-medium uppercase text-[0.7rem]">No.
                                Order</span>
                            <span
                                class="text-black font-bold text-sm mt-1 block">#<?= htmlspecialchars($tx['no_order']) ?></span>
                        </div>
                        <div class="p-4">
                            <span class="text-[#64748b] block font-medium uppercase text-[0.7rem]">Waktu
                                Transaksi</span>
                            <span
                                class="text-black font-bold text-xs mt-1 block"><?= date('d M Y, H:i', strtotime($tx['tanggal'])) ?></span>
                        </div>
                        <div class="p-4">
                            <span class="text-[#64748b] block font-medium uppercase text-[0.7rem]">Kasir
                                Bertugas</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="text-black font-bold text-xs"><?= htmlspecialchars($tx['nama_kasir'] ?? 'Kasir #' . $tx['id_kasir']) ?></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <span class="text-[#64748b] block font-medium uppercase text-[0.7rem]">Total
                                Item</span>
                            <span class="text-black font-bold text-sm mt-1 block"><?= $total_item_qty ?> Barang
                                (<?= count($items) ?>)</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-7 py-5 border-b border-[#e2e8f0] flex items-center">
                        <div>
                            <h3 class="text-base font-bold text-[#1e293b]">Daftar Item Pembelian</h3>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-[#f1f5f9] text-xs font-semibold text-[#94a3b8] uppercase">
                                    <th scope="col" class="px-6 py-4 text-center">No</th>
                                    <th scope="col" class="px-6 py-4">Nama Produk</th>
                                    <th scope="col" class="px-6 py-4 text-right">Harga Satuan</th>
                                    <th scope="col" class="px-6 py-4 text-center">Qty</th>
                                    <th scope="col" class="px-6 py-4 text-right">Diskon</th>
                                    <th scope="col" class="px-6 py-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f8fafc]">
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-[#94a3b8]">
                                            <i class="fa-solid fa-box-open text-3xl mb-2 text-[#cbd5e1] block"></i>
                                            <span class="block font-medium text-xs text-[#64748b]">Belum ada item dalam
                                                transaksi ini.</span>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $idx => $item): ?>
                                        <tr class="hover:bg-[#fafbff] transition-colors">
                                            <td class="px-6 py-4 text-center text-xs text-black font-bold">
                                                <?= $idx + 1 ?>
                                            </td>
                                            <td class="px-6 py-4 font-semibold text-[#1e293b]">
                                                <div>
                                                    <span
                                                        class="block text-black font-bold"><?= htmlspecialchars($item['nama_produk'] ?? 'Produk #' . $item['id_produk']) ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right text-black font-bold">
                                                Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                                            </td>
                                            <td class="px-6 py-4 text-center font-bold text-[#1e293b]">
                                                <?= htmlspecialchars($item['kuantitas']) ?>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <?php if ($item['diskon'] > 0): ?>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs text-black font-bold">
                                                        -Rp <?= number_format($item['diskon'] * $item['kuantitas'], 0, ',', '.') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-[#cbd5e1]">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-right text-black font-bold">
                                                Rp <?= number_format($item['harga_jual'], 0, ',', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="px-6 py-3.5 border-t border-[#e2e8f0] bg-white flex items-center justify-between text-xs text-[#64748b]">
                        <span>Total Kuantitas: <strong class="text-black font-bold"><?= $total_item_qty ?>
                                item</strong></span>
                        <span>Subtotal Barang: <strong class="text-black font-bold">Rp
                                <?= number_format($subtotal_items, 0, ',', '.') ?></strong></span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-6 py-5 border-b border-[#e2e8f0] bg-white text-center">
                        <div class="flex items-center justify-center text-[1.25rem] font-extrabold tracking-tight mb-1">
                            <span class="text-[#003399]">Indo</span><span class="text-[#E30613]">maret</span>
                        </div>
                        <div class="mt-2 text-[0.72rem] text-[#94a3b8] flex items-center justify-center gap-2">
                            <span><?= date('d/m/Y H:i:s', strtotime($tx['tanggal'])) ?></span>
                            <span><?= htmlspecialchars($tx['kode_transaksi']) ?></span>
                        </div>
                    </div>

                    <div class="p-6 space-y-3.5 text-xs">
                        <div class="flex items-center justify-between text-black font-bold">
                            <span>Subtotal Item (<?= $total_item_qty ?> pcs)</span>
                            <span class="text-black font-bold">Rp
                                <?= number_format($subtotal_items, 0, ',', '.') ?></span>
                        </div>

                        <?php if (!empty($tx['id_donasi']) && !empty($tx['harga_donasi'])): ?>
                            <div class="flex items-center justify-between text-black font-bold">
                                <span class="flex items-center gap-1.5">
                                    <span>Donasi (<?= htmlspecialchars($tx['nama_donasi'] ?? 'Donasi') ?>)</span>
                                </span>
                                <span class="font-bold">+Rp <?= number_format($tx['harga_donasi'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="pt-3 border-t-2 border-dashed border-[#e2e8f0]">
                            <div class="flex items-baseline justify-between">
                                <span class="text-sm font-bold text-[#1e293b]">Total Pembayaran</span>
                                <span class="text-xl text-black font-extrabold">
                                    Rp <?= number_format($tx['total'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#f1f5f9] space-y-2">
                            <div class="text-[0.7rem] uppercase text-black font-bold tracking-wider mb-2">Rincian
                                Pembayaran</div>

                            <?php if ($tx['QRIS'] > 0): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-black font-bold flex items-center gap-1.5">
                                        Bayar QRIS
                                    </span>
                                    <span class="font-bold text-[#1e293b]">Rp
                                        <?= number_format($tx['QRIS'], 0, ',', '.') ?></span>
                                </div>
                            <?php else: ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-black font-bold flex items-center gap-1.5">Tunai Diterima</span>
                                    <span class="font-bold text-[#1e293b]">Rp
                                        <?= number_format($tx['tunai'], 0, ',', '.') ?></span>
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-black font-bold">Kembalian</span>
                                    <span
                                        class="font-bold <?= $tx['kembalian'] > 0 ? 'text-black font-bold' : 'text-black font-bold' ?>">
                                        Rp <?= number_format($tx['kembalian'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="pt-5 mt-4 border-t border-dashed border-[#e2e8f0] text-center space-y-1">
                            <p class="text-[0.7rem] text-[#94a3b8]">Terima kasih telah berbelanja di Indomaret</p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-[#f8fafc] border-t border-[#e2e8f0] flex flex-col gap-2">
                        <a href="/pages/transactions/list.php"
                            class="w-full bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#64748b] text-center rounded-xl py-2.5 text-xs font-semibold transition no-underline">
                            Daftar Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$alertEntity = 'transaksi';
$alertDeleteUrl = '/process/transactions_process.php?action=delete&id=';
include ROOTPATH . '/includes/alert.php';
?>
</body>

</html>