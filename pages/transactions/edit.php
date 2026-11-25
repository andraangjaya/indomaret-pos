<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

$kode_transaksi = $_GET['kode_transaksi'] ?? $_GET['id'] ?? '';

if (empty($kode_transaksi)) {
    header("Location: /pages/transactions/list.php");
    exit();
}

$sql = "SELECT * FROM transaksi WHERE kode_transaksi = '$kode_transaksi'";
$result = mysqli_query($connection, $sql);
if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: /pages/transactions/list.php");
    exit();
}
$data = mysqli_fetch_assoc($result);

$existing_items = [];
$sql_items = "SELECT dt.id_produk, p.nama_produk, dt.harga, dt.diskon, dt.kuantitas
              FROM detail_transaksi dt
              LEFT JOIN produk p ON dt.id_produk = p.id_produk
              WHERE dt.kode_transaksi = '$kode_transaksi'
              ORDER BY dt.id_detail ASC";
$res_items = mysqli_query($connection, $sql_items);
if ($res_items) {
    while ($row = mysqli_fetch_assoc($res_items)) {
        $existing_items[] = [
            'id' => (string) $row['id_produk'],
            'nama' => $row['nama_produk'] ?? ('Produk #' . $row['id_produk']),
            'harga' => (float) $row['harga'],
            'diskon' => (float) $row['diskon'],
            'qty' => (int) $row['kuantitas']
        ];
    }
}

$res_kasir = mysqli_query($connection, "SELECT id_kasir, nama_kasir FROM kasir ORDER BY nama_kasir ASC");
$res_donasi = mysqli_query($connection, "SELECT id_donasi, nama_donasi, harga_donasi FROM donasi ORDER BY nama_donasi ASC");
$res_produk = mysqli_query($connection, "SELECT id_produk, nama_produk, harga, diskon FROM produk ORDER BY nama_produk ASC");

$initial_method = ($data['QRIS'] > 0) ? 'qris' : 'tunai';
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
                    <h1 class="text-xl md:text-2xl font-extrabold text-[#1e293b] tracking-tight">Edit Transaksi</h1>
                </div>
            </div>
        </div>
    </div>

    <form action="/process/transactions_process.php" method="post" id="formTransaction">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="kode_transaksi" value="<?= htmlspecialchars($data['kode_transaksi']) ?>">
        <input type="hidden" name="total" id="inputTotal" value="<?= htmlspecialchars($data['total']) ?>">
        <input type="hidden" name="kembalian" id="inputKembalian" value="<?= htmlspecialchars($data['kembalian']) ?>">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-7 py-5 border-b border-[#e2e8f0] flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                            <div>
                                <h2 class="text-base font-bold text-[#1e293b]">Informasi Transaksi</h2>
                                <p class="text-xs font-bold text-black">
                                    <?= htmlspecialchars($data['kode_transaksi']) ?>
                                </p>
                            </div>
                        </div>
                        <div id="headerPaymentBadge">
                            <?php if ($initial_method === 'qris'): ?>
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
                        class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-[#e2e8f0] border-b border-[#e2e8f0] bg-[#f8fafc] text-xs">
                        <div class="p-4">
                            <label for="no_order" class="text-[#64748b] block font-medium uppercase text-[0.7rem]">No.
                                Order</label>
                            <input type="text" id="no_order" name="no_order" readonly
                                value="<?= htmlspecialchars($data['no_order']) ?>"
                                class="w-full mt-1 px-2.5 py-1.5 border border-[#e2e8f0] rounded-lg text-xs bg-[#f1f5f9] text-black font-bold outline-none cursor-not-allowed">
                        </div>

                        <div class="p-4">
                            <label for="id_kasir" class="text-[#64748b] block font-medium uppercase text-[0.7rem]">Kasir
                                Bertugas</label>
                            <select id="id_kasir" name="id_kasir" required
                                class="w-full mt-1 px-2.5 py-1.5 border border-[#e2e8f0] rounded-lg text-xs bg-white text-black font-bold focus:border-[#003399] outline-none">
                                <?php if ($res_kasir): ?>
                                    <?php while ($k = mysqli_fetch_assoc($res_kasir)): ?>
                                        <option value="<?= $k['id_kasir'] ?>" <?= ($k['id_kasir'] == $data['id_kasir']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($k['nama_kasir']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="p-4">
                            <label for="id_donasi"
                                class="text-[#64748b] block font-medium uppercase text-[0.7rem]">Program Donasi</label>
                            <select id="id_donasi" name="id_donasi" onchange="calculateCart()"
                                class="w-full mt-1 px-2.5 py-1.5 border border-[#e2e8f0] rounded-lg text-xs bg-white text-black font-bold focus:border-[#003399] outline-none">
                                <option value="" data-harga="0">Tanpa Donasi</option>
                                <?php if ($res_donasi): ?>
                                    <?php while ($d = mysqli_fetch_assoc($res_donasi)): ?>
                                        <option value="<?= $d['id_donasi'] ?>" data-harga="<?= $d['harga_donasi'] ?>"
                                            <?= ($d['id_donasi'] == $data['id_donasi']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($d['nama_donasi']) ?> (+Rp
                                            <?= number_format($d['harga_donasi'], 0, ',', '.') ?>)
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="p-4">
                            <span class="text-[#64748b] block font-medium uppercase text-[0.7rem]">Total Item</span>
                            <input type="text" id="readonlyTotalItem" readonly value="0 Barang"
                                class="w-full mt-1 bg-transparent text-black font-bold text-sm mt-1 block outline-none border-none p-0 cursor-default">
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

                    <div class="p-6 bg-[#f8fafc] border-b border-[#e2e8f0]">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                            <div class="sm:col-span-5 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-[#1e293b]">Pilih Produk</label>
                                <select id="pickerProduct" onchange="syncPickerData()"
                                    class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-white focus:border-[#003399] outline-none">
                                    <option value="">Cari / Pilih Produk</option>
                                    <?php if ($res_produk): ?>
                                        <?php while ($p = mysqli_fetch_assoc($res_produk)): ?>
                                            <option value="<?= $p['id_produk'] ?>"
                                                data-nama="<?= htmlspecialchars($p['nama_produk']) ?>"
                                                data-harga="<?= $p['harga'] ?>" data-diskon="<?= $p['diskon'] ?>">
                                                <?= htmlspecialchars($p['nama_produk']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="sm:col-span-2 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-[#1e293b]">Harga Satuan</label>
                                <input type="number" id="pickerHarga" readonly
                                    class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-gray-100 text-black font-bold outline-none"
                                    placeholder="0">
                            </div>

                            <div class="sm:col-span-2 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-[#1e293b]">Diskon</label>
                                <input type="number" id="pickerDiskon" readonly
                                    class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-gray-100 text-black font-bold outline-none"
                                    placeholder="0">
                            </div>

                            <div class="sm:col-span-1 flex flex-col gap-1.5">
                                <label class="text-xs font-semibold text-[#1e293b]">Qty</label>
                                <input type="number" id="pickerQty" min="1" value="1"
                                    class="w-full px-2.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-white text-center font-bold focus:border-[#003399] outline-none">
                            </div>

                            <div class="sm:col-span-2">
                                <button type="button" onclick="addProductToCart()"
                                    class="w-full bg-[#003399] hover:bg-[#002277] text-white py-2.5 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                                    <i class="fa-solid fa-plus text-xs"></i> Tambah
                                </button>
                            </div>
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
                                    <th scope="col" class="px-6 py-4 text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody" class="divide-y divide-[#f8fafc]"></tbody>
                        </table>
                    </div>

                    <div
                        class="px-6 py-3.5 border-t border-[#e2e8f0] bg-white flex items-center justify-between text-xs text-[#64748b]">
                        <span>Total Kuantitas: <strong id="summaryQty" class="text-black font-bold">0
                                item</strong></span>
                        <span>Subtotal Barang: <strong id="summaryItemSubtotal" class="text-black font-bold">Rp
                                0</strong></span>
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
                            <span><?= date('d/m/Y H:i:s', strtotime($data['tanggal'])) ?></span>
                            <span><?= htmlspecialchars($data['kode_transaksi']) ?></span>
                        </div>
                    </div>

                    <div class="p-6 space-y-3.5 text-xs">
                        <div class="flex items-center justify-between text-black font-bold">
                            <span>Subtotal Item (<span id="receiptItemQty">0</span> pcs)</span>
                            <span class="text-black font-bold" id="labelSubtotal">Rp 0</span>
                        </div>

                        <div class="flex items-center justify-between text-black font-bold">
                            <span class="flex items-center gap-1.5">
                                <span id="labelDonasiTitle">Donasi</span>
                            </span>
                            <span class="font-bold" id="labelDonasi">+Rp 0</span>
                        </div>

                        <div class="pt-3 border-t-2 border-dashed border-[#e2e8f0]">
                            <div class="flex items-baseline justify-between">
                                <span class="text-sm font-bold text-[#1e293b]">Total Pembayaran</span>
                                <span class="text-xl text-black font-extrabold" id="displayTotal">
                                    Rp <?= number_format($data['total'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#f1f5f9] space-y-3">
                            <div class="text-[0.7rem] uppercase text-black font-bold tracking-wider mb-2">Rincian
                                Pembayaran</div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="tunai"
                                        <?= ($initial_method === 'tunai') ? 'checked' : '' ?>
                                        onchange="togglePaymentMethod('tunai')" class="peer sr-only">
                                    <div
                                        class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-[#e2e8f0] bg-[#fafbfc] text-[#64748b] font-semibold peer-checked:bg-emerald-100 peer-checked:border-emerald-200 peer-checked:text-emerald-800 transition">
                                        <i class="fa-solid fa-money-bill-wave text-[0.7rem]"></i>
                                        <span>TUNAI</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="qris" <?= ($initial_method === 'qris') ? 'checked' : '' ?> onchange="togglePaymentMethod('qris')" class="peer sr-only">
                                    <div
                                        class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-[#e2e8f0] bg-[#fafbfc] text-[#64748b] font-semibold peer-checked:bg-purple-100 peer-checked:border-purple-200 peer-checked:text-purple-800 transition">
                                        <i class="fa-solid fa-qrcode text-[0.7rem]"></i>
                                        <span>QRIS</span>
                                    </div>
                                </label>
                            </div>

                            <div id="tunaiSection"
                                class="space-y-2.5 pt-1 <?= ($initial_method === 'qris') ? 'hidden' : '' ?>">
                                <div class="flex items-center justify-between">
                                    <label for="tunai" class="text-black font-bold flex items-center gap-1.5">Tunai
                                        Diterima</label>
                                </div>
                                <input type="number" id="tunai" name="tunai" placeholder="0" min="0"
                                    value="<?= htmlspecialchars($data['tunai']) ?>" oninput="calculateChange()"
                                    class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-[#fafbfc] focus:bg-white focus:border-[#003399] outline-none font-bold text-[#1e293b]">

                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-black font-bold">Kembalian</span>
                                    <span class="text-black font-bold" id="labelKembalian">
                                        Rp <?= number_format($data['kembalian'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>

                            <div id="qrisSection"
                                class="space-y-2 pt-1 <?= ($initial_method === 'tunai') ? 'hidden' : '' ?>">
                                <label for="qris" class="text-black font-bold flex items-center gap-1.5">Bayar
                                    QRIS</label>
                                <input type="number" id="qris" name="QRIS"
                                    value="<?= ($initial_method === 'qris') ? htmlspecialchars($data['total']) : 0 ?>"
                                    readonly
                                    class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-[#f1f5f9] text-[#1e293b] outline-none font-bold cursor-not-allowed">
                            </div>
                        </div>

                        <div class="pt-5 mt-4 border-t border-dashed border-[#e2e8f0] text-center space-y-1">
                            <p class="text-[0.7rem] text-[#94a3b8]">Terima kasih telah berbelanja di Indomaret</p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-[#f8fafc] border-t border-[#e2e8f0] flex flex-col gap-2">
                        <button type="submit"
                            class="w-full bg-[#003399] hover:bg-[#002277] text-white text-center rounded-xl py-2.5 text-xs font-semibold transition flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                        <a href="/pages/transactions/list.php"
                            class="w-full bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#64748b] text-center rounded-xl py-2.5 text-xs font-semibold transition no-underline">
                            Daftar Transaksi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    window.transactionConfig = {
        cart: <?= json_encode($existing_items) ?>,
        paymentMethod: '<?= $initial_method ?>',
        variant: 'edit',
        renderOnLoad: true
    };
</script>
<script src="/assets/js/transactions.js"></script>

</body>

</html>