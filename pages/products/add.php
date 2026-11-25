<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
?>

<div class="min-h-[calc(100vh-72px)] flex items-center justify-center p-6">
    <div
        class="bg-white w-full max-w-[440px] rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] p-8">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-[#e2e8f0]">
            <i class="fa-solid fa-boxes-stacked text-[1.6rem] text-[#d97706] bg-[#fffbeb] p-3 rounded-xl"></i>
            <div>
                <h2 class="text-[1.2rem] font-bold text-[#1e293b]">Tambah Produk Baru</h2>
                <p class="text-[0.8rem] text-[#64748b] mt-0.5">Masukkan informasi data produk</p>
            </div>
        </div>

        <form action="/process/products_process.php" method="post" class="flex flex-col gap-[18px]">
            <input type="hidden" name="action" value="add">

            <div class="flex flex-col gap-2.5">
                <label for="nama_produk" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" placeholder="Nama Produk" required
                    autocomplete="off"
                    class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
            </div>

            <div class="flex flex-col gap-2.5">
                <label for="harga" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Harga</label>
                <input type="number" id="harga" name="harga" placeholder="Harga Produk (Rp)" required
                    autocomplete="off" min="0"
                    class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
            </div>

            <div class="flex flex-col gap-2.5">
                <label for="diskon" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Diskon</label>
                <input type="number" id="diskon" name="diskon" placeholder="Diskon (Rp)"
                    autocomplete="off" min="0" value="0"
                    class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
            </div>

            <button type="submit"
                class="mt-2 bg-[#d97706] hover:bg-[#b45309] text-white border-none rounded-lg py-3 text-[0.9rem] font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Produk
            </button>
        </form>
    </div>
</div>
</body>

</html>
