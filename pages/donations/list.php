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
                <i class="fa-solid fa-hand-holding-heart text-[1.4rem] text-[#059669] bg-[#ecfdf5] p-3 rounded-xl"></i>
                <div>
                    <h2 class="text-[1.1rem] font-bold text-[#1e293b]">Daftar Donasi</h2>
                    <p class="text-[0.78rem] text-[#64748b] mt-0.5">Kelola semua program donasi</p>
                </div>
            </div>
            <a href="/pages/donations/add.php"
                class="inline-flex items-center gap-2 bg-[#059669] hover:bg-[#047857] text-white no-underline rounded-lg px-4 py-2.5 text-[0.85rem] font-semibold transition-colors duration-200 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                Tambah Donasi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="border-b border-[#f1f5f9]">
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8] text-center">ID Donasi</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8]">Nama Donasi</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8]">Harga Donasi</th>
                        <th scope="col" class="px-6 py-4 font-medium text-[#94a3b8] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT id_donasi, nama_donasi, harga_donasi FROM donasi ORDER BY id_donasi ASC";
                    $result = mysqli_query($connection, $sql);

                    if (mysqli_num_rows($result) === 0) {
                        ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#64748b]">
                                <i class="fa-solid fa-heart-crack text-4xl text-[#cbd5e1] block mb-3"></i>
                                <p class="text-[0.9rem]">Belum ada data donasi.
                                    <a href="/pages/donations/add.php"
                                        class="text-[#059669] font-semibold hover:underline">Tambah donasi baru</a>.
                                </p>
                            </td>
                        </tr>
                        <?php
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr
                                class="border-b border-[#f8fafc] hover:bg-[#fafbff] transition-colors duration-150 last:border-none">

                                <td class="px-6 py-5 text-center font-semibold text-[#1e293b]">
                                    <?= htmlspecialchars($row["id_donasi"]) ?>
                                </td>

                                <td class="px-6 py-5 font-semibold text-[#1e293b]">
                                    <?= htmlspecialchars($row["nama_donasi"]) ?>
                                </td>

                                <td class="px-6 py-5 font-semibold text-[#1e293b]">
                                    Rp <?= number_format($row["harga_donasi"], 0, ',', '.') ?>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-2.5">
                                        <a href="edit.php?id_donasi=<?= urlencode($row['id_donasi']) ?>"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-md border border-emerald-300 bg-emerald-100/50 px-5 py-1.5 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100 min-w-[80px] no-underline">
                                            <i class="fa-solid fa-pen-to-square text-[0.7rem]"></i>
                                            Edit
                                        </a>

                                        <button type="button"
                                            onclick="confirmDelete(event, '<?= $row['id_donasi'] ?>', '<?= htmlspecialchars(addslashes($row['nama_donasi'])) ?>')"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-md border border-red-300 bg-red-100/50 px-5 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-100 min-w-[80px] cursor-pointer">
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
$alertEntity = 'donasi';
$alertDeleteUrl = '/process/donations_process.php?action=delete&id=';
include ROOTPATH . '/includes/alert.php';
?>
</body>

</html>