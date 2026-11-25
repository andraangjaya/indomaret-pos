<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__, 2) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

if (isset($_GET["id_kasir"])) {
    $id_kasir = $_GET["id_kasir"];
    $sql = "SELECT * FROM kasir WHERE id_kasir = '$id_kasir'";

    $result = mysqli_query($connection, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
    } else {
        header("location:/pages/cashiers/list.php");
        exit();
    }
}
?>

    <div class="min-h-[calc(100vh-72px)] flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-[440px] rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] p-8">

            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-[#e2e8f0]">
                <i class="fa-solid fa-user-pen text-[1.6rem] text-[#003399] bg-[#f0f4ff] p-3 rounded-xl"></i>
                <div>
                    <h2 class="text-[1.2rem] font-bold text-[#1e293b]">Edit Data Kasir</h2>
                    <p class="text-[0.8rem] text-[#64748b] mt-0.5">Ubah informasi data kasir terminal</p>
                </div>
            </div>

            <form action="/process/cashiers_process.php" method="post" class="flex flex-col gap-[18px]">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_kasir" value="<?= htmlspecialchars($data['id_kasir']); ?>">

                <div class="flex flex-col gap-2.5">
                    <label for="id_kasir" class="text-[0.72rem] font-extrabold text-[#003399] tracking-[0.25px]">ID Kasir</label>
                    <input type="text" id="id_kasir" name="id_kasir" placeholder="ID Kasir"
                        value="<?= htmlspecialchars($data['id_kasir'] ?? ''); ?>" disabled
                        class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] bg-[#f1f5f9] text-[#94a3b8] outline-none cursor-not-allowed">
                </div>

                <div class="flex flex-col gap-2.5">
                    <label for="nama_kasir" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Nama Kasir</label>
                    <input type="text" id="nama_kasir" name="nama_kasir" placeholder="Nama Kasir"
                        value="<?= htmlspecialchars($data['nama_kasir']); ?>" required autocomplete="off"
                        class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
                </div>

                <div class="flex flex-col gap-2.5">
                    <label for="no_telp" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Nomor Telepon</label>
                    <input type="text" id="no_telp" name="no_telp" placeholder="Nomor Telepon"
                        value="<?= htmlspecialchars($data['no_telp']); ?>" required autocomplete="off"
                        class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
                </div>

                <div class="flex flex-col gap-2.5">
                    <label for="alamat" class="text-[0.72rem] font-semibold text-[#1e293b] tracking-[0.25px]">Alamat</label>
                    <input type="text" id="alamat" name="alamat" placeholder="Alamat"
                        value="<?= htmlspecialchars($data['alamat']); ?>" required autocomplete="off"
                        class="w-full px-3.5 py-3 border border-[#e2e8f0] rounded-lg text-[0.9rem] text-[#1e293b] bg-[#fafbfc] outline-none transition-all duration-200 placeholder:text-[#94a3b8] focus:bg-white focus:border-[#003399] focus:shadow-[0_0_0_3px_rgba(0,51,153,0.1)]">
                </div>

                <button type="submit"
                    class="mt-2 bg-[#003399] hover:bg-[#002277] text-white border-none rounded-lg py-3 text-[0.9rem] font-semibold cursor-pointer transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Update Kasir
                </button>
            </form>
        </div>
    </div>
</body>

</html>
