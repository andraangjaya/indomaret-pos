<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

if (isset($_GET["id_donasi"])) {
    $id_donasi = $_GET["id_donasi"];
    $sql = "SELECT * FROM donasi WHERE id_donasi = '$id_donasi'";

    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        header("location:/pages/donations/list.php");
        exit();
    }
}
?>

<div class="min-h-[calc(100vh-72px)] flex items-center justify-center p-6">
    <div
        class="bg-white w-full max-w-[440px] rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] p-8">

        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-[#e2e8f0]">
            <i class="fa-solid fa-hand-holding-heart text-[1.6rem] text-[#059669] bg-[#ecfdf5] p-3 rounded-xl"></i>
            <div>
                <h2 class="text-[1.2rem] font-bold text-[#1e293b]">Edit Data Donasi</h2>
                <p class="text-[0.8rem] text-[#64748b] mt-0.5">Ubah informasi data program donasi</p>
            </div>
        </div>

        <form action="/process/donations_process.php" method="post" class="flex flex-col gap-[18px]">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_donasi" value="<?= htmlspecialchars($data['id_donasi']); ?>">

            <div class="flex flex-col gap-2.5">
                <label for="id_donasi" class="text-[0.72rem] font-extrabold text-[#059669] tracking-[0.25px]">ID Donasi</label>
                <input type="text" id="id_donasi" name="id_donasi" placeholder="ID Donasi"
                    value="<?= htmlspecialchars($data['id_donasi'] ?? ''); ?>" disabled
                    class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] bg-[#f1f5f9] text-[#94a3b8] outline-none cursor-not-allowed">
            </div>

            <div class="flex flex-col gap-2.5">
                <label for="nama_donasi" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Nama Donasi</label>
                <input type="text" id="nama_donasi" name="nama_donasi" placeholder="Nama Donasi"
                    value="<?= htmlspecialchars($data['nama_donasi']); ?>" required autocomplete="off"
                    class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
            </div>

            <div class="flex flex-col gap-2.5">
                <label for="harga_donasi" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Harga Donasi</label>
                <input type="number" id="harga_donasi" name="harga_donasi" placeholder="Harga Donasi (Rp)"
                    value="<?= htmlspecialchars($data['harga_donasi']); ?>" required autocomplete="off" min="0"
                    class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
            </div>

            <button type="submit"
                class="mt-2 bg-[#059669] hover:bg-[#047857] text-white border-none rounded-lg py-3 text-[0.9rem] font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Update Donasi
            </button>
        </form>
    </div>
</div>
</body>

</html>
