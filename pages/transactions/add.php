<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

$next_no_order = 'A01';
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
        $next_no_order = $letter . str_pad($num, 2, '0', STR_PAD_LEFT);
    }
}

$res_kasir = mysqli_query($connection, "SELECT id_kasir, nama_kasir FROM kasir ORDER BY nama_kasir ASC");
$res_donasi = mysqli_query($connection, "SELECT id_donasi, nama_donasi, harga_donasi FROM donasi ORDER BY nama_donasi ASC");
$res_produk = mysqli_query($connection, "SELECT id_produk, nama_produk, harga, diskon FROM produk ORDER BY nama_produk ASC");
?>

<div class="p-6 md:p-8 max-w-[1400px] mx-auto space-y-6">
    <div class="flex items-center justify-between pb-2">
        <div class="flex items-center gap-3">
            <a href="/pages/transactions/list.php"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-[#e2e8f0] text-[#64748b] hover:text-[#003399] hover:border-[#003399] transition shadow-sm">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-[#1e293b] tracking-tight">Tambah Transaksi Baru</h1>
            </div>
        </div>
    </div>

    <form action="/process/transactions_process.php" method="post" id="formTransaction">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="total" id="inputTotal" value="0">
        <input type="hidden" name="kembalian" id="inputKembalian" value="0">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-7 py-5 border-b border-[#e2e8f0] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div>
                                <h2 class="text-base font-bold text-[#1e293b]">Informasi Transaksi
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="id_kasir" class="text-xs font-semibold text-[#1e293b]">Kasir Bertugas</label>
                            <select id="id_kasir" name="id_kasir" required
                                class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-black font-bold text-xs bg-[#fafbfc] focus:bg-white focus:border-[#003399] outline-none">
                                <?php if ($res_kasir): ?>
                                    <?php while ($k = mysqli_fetch_assoc($res_kasir)): ?>
                                        <option value="<?= $k['id_kasir'] ?>">
                                            <?= htmlspecialchars($k['nama_kasir']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="no_order" class="text-xs font-semibold text-[#1e293b]">No. Order</label>
                            <input type="text" id="no_order" name="no_order" readonly
                                value="<?= htmlspecialchars($next_no_order) ?>"
                                class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-[#f1f5f9] text-black font-bold outline-none font-bold cursor-not-allowed">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="id_donasi" class="text-xs font-semibold text-[#1e293b]">Program Donasi</label>
                            <select id="id_donasi" name="id_donasi" onchange="calculateCart()"
                                class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-black font-bold text-xs bg-[#fafbfc] focus:bg-white focus:border-[#003399] outline-none">
                                <option value="" data-harga="0">Tanpa Donasi (Rp 0)</option>
                                <?php if ($res_donasi): ?>
                                    <?php while ($d = mysqli_fetch_assoc($res_donasi)): ?>
                                        <option value="<?= $d['id_donasi'] ?>" data-harga="<?= $d['harga_donasi'] ?>">
                                            <?= htmlspecialchars($d['nama_donasi']) ?> (+Rp
                                            <?= number_format($d['harga_donasi'], 0, ',', '.') ?>)
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-7 py-5 border-b border-[#e2e8f0] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div>
                                <h2 class="text-base font-bold text-[#1e293b]">Daftar Item Pembelian</h2>
                            </div>
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
                                    <th class="px-6 py-3.5 text-center">No</th>
                                    <th class="px-6 py-3.5">Produk</th>
                                    <th class="px-6 py-3.5 text-right">Harga</th>
                                    <th class="px-6 py-3.5 text-center">Qty</th>
                                    <th class="px-6 py-3.5 text-right">Diskon</th>
                                    <th class="px-6 py-3.5 text-right">Subtotal</th>
                                    <th class="px-6 py-3.5 text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody" class="divide-y divide-[#f8fafc]">
                                <tr id="emptyRow">
                                    <td colspan="7" class="px-6 py-12 text-center text-[#94a3b8]">
                                        <i class="fa-solid fa-basket-shopping text-3xl mb-2 text-[#cbd5e1] block"></i>
                                        <span class="block font-medium text-xs text-[#64748b]">Belum ada produk yang
                                            dimasukkan ke transaksi ini.</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="px-7 py-3.5 border-t border-[#e2e8f0] bg-white flex items-center justify-between text-xs text-[#64748b]">
                        <span>Total Kuantitas: <strong id="summaryQty" class="text-[#1e293b]">0 pcs</strong></span>
                        <span>Subtotal Barang: <strong id="summaryItemSubtotal" class="text-black">Rp
                                0</strong></span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <div
                    class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
                    <div class="px-6 py-5 border-b border-[#e2e8f0] text-center">
                        <span class="text-xs font-bold text-black uppercase tracking-wider block">Total Tagihan
                            Transaksi</span>
                        <div class="text-3xl font-extrabold text-[#1e293b] mt-1" id="displayTotal">
                            Rp 0
                        </div>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="space-y-2.5 pb-4 border-b border-[#f1f5f9]">
                            <div class="flex items-center justify-between text-black font-bold">
                                <span>Subtotal Produk</span>
                                <span class="font-semibold text-[#1e293b]" id="labelSubtotal">Rp 0</span>
                            </div>
                            <div class="flex items-center justify-between text-black font-bold">
                                <span>Program Donasi</span>
                                <span class="font-bold" id="labelDonasi">+Rp 0</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-xs font-bold text-[#1e293b] uppercase tracking-wide block">Metode
                                Pembayaran</label>

                            <div class="grid grid-cols-2 gap-2.5">
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="tunai" checked
                                        onchange="togglePaymentMethod('tunai')" class="peer sr-only">
                                    <div
                                        class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-[#e2e8f0] bg-[#fafbfc] text-[#64748b] font-semibold peer-checked:bg-[#ecfdf5] peer-checked:border-emerald-400 peer-checked:text-emerald-700 transition">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        <span>Tunai</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="qris"
                                        onchange="togglePaymentMethod('qris')" class="peer sr-only">
                                    <div
                                        class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-[#e2e8f0] bg-[#fafbfc] text-[#64748b] font-semibold peer-checked:bg-[#faf5ff] peer-checked:border-purple-400 peer-checked:text-[#7c3aed] transition">
                                        <i class="fa-solid fa-qrcode"></i>
                                        <span>QRIS</span>
                                    </div>
                                </label>
                            </div>

                            <div id="tunaiSection" class="space-y-3 pt-1">
                                <div class="space-y-1.5">
                                    <label for="tunai"
                                        class="text-xs font-semibold text-[#64748b] flex items-center justify-between">
                                        <span>Nominal Diterima</span>
                                    </label>
                                    <input type="number" id="tunai" name="tunai" placeholder="0" min="0" value="0"
                                        oninput="calculateChange()"
                                        class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-[#fafbfc] focus:bg-white focus:border-[#003399] outline-none font-semibold text-[#1e293b]">
                                </div>

                                <div
                                    class="p-3 bg-[#f8fafc] border border-[#e2e8f0] rounded-xl flex items-center justify-between">
                                    <span class="font-semibold text-[#64748b]">Kembalian</span>
                                    <span class="text-base font-extrabold text-black" id="labelKembalian">Rp
                                        0</span>
                                </div>
                            </div>

                            <div id="qrisSection" class="space-y-1.5 pt-1 hidden">
                                <label for="qris"
                                    class="text-xs font-semibold text-[#64748b] flex items-center justify-between">
                                    <span>Nominal Bayar</span>
                                </label>
                                <input type="number" id="qris" name="QRIS" value="0" readonly
                                    class="w-full px-3.5 py-2.5 border border-[#e2e8f0] rounded-xl text-xs bg-[#f1f5f9] font-bold text-black outline-none font-bold cursor-not-allowed">
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit"
                                class="w-full bg-[#003399] hover:bg-[#002277] text-white py-3 rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer shadow-md">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Simpan Transaksi</span>
                            </button>
                            <p class="text-[0.68rem] text-center text-[#94a3b8] mt-2">
                                Transaksi akan langsung tercatat dan dapat dilihat struk rinciannya
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    window.transactionConfig = {
        cart: [],
        paymentMethod: 'tunai',
        variant: 'add',
        renderOnLoad: false
    };
</script>
<script src="/assets/js/transactions.js"></script>
</body>

</html>