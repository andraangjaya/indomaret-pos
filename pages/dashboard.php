<?php
if (!defined('ROOTPATH')) {
    define('ROOTPATH', dirname(__DIR__) . '/');
}
include ROOTPATH . '/includes/header.php';
include ROOTPATH . '/config/config.php';

$r_kasir = mysqli_query($connection, "SELECT COUNT(*) as total FROM kasir");
$total_kasir = mysqli_fetch_assoc($r_kasir)['total'] ?? 0;

$r_kasir_list = mysqli_query($connection, "SELECT id_kasir, nama_kasir, no_telp, alamat FROM kasir ORDER BY id_kasir ASC LIMIT 4");

$r_produk = mysqli_query($connection, "SELECT COUNT(*) as total, AVG(harga) as avg_harga, SUM(CASE WHEN diskon > 0 THEN 1 ELSE 0 END) as total_diskon FROM produk");
$p_stat = mysqli_fetch_assoc($r_produk);
$total_produk = $p_stat['total'] ?? 0;
$avg_harga = round($p_stat['avg_harga'] ?? 0);
$total_diskon = $p_stat['total_diskon'] ?? 0;

$r_produk_list = mysqli_query($connection, "SELECT id_produk, nama_produk, harga, diskon FROM produk ORDER BY id_produk ASC LIMIT 4");

$r_donasi_prog = mysqli_query($connection, "SELECT COUNT(*) as total FROM donasi");
$total_donasi_program = mysqli_fetch_assoc($r_donasi_prog)['total'] ?? 0;

$r_donasi_sum = mysqli_query($connection, "SELECT COUNT(*) as tx_count, COALESCE(SUM(d.harga_donasi), 0) as total_donasi FROM transaksi t JOIN donasi d ON t.id_donasi = d.id_donasi");
$d_stat = mysqli_fetch_assoc($r_donasi_sum);
$tx_donasi_count = $d_stat['tx_count'] ?? 0;
$total_donasi_terkumpul = $d_stat['total_donasi'] ?? 0;

$r_donasi_list = mysqli_query($connection, "SELECT id_donasi, nama_donasi, harga_donasi FROM donasi ORDER BY id_donasi ASC LIMIT 4");

$r_tx = mysqli_query($connection, "SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as total_omset, COALESCE(SUM(tunai - kembalian), 0) as total_tunai, COALESCE(SUM(QRIS), 0) as total_qris FROM transaksi");
$tx_stat = mysqli_fetch_assoc($r_tx);
$total_transaksi = $tx_stat['total'] ?? 0;
$total_omset = $tx_stat['total_omset'] ?? 0;
$total_tunai = $tx_stat['total_tunai'] ?? 0;
$total_qris = $tx_stat['total_qris'] ?? 0;

$r_tx_list = mysqli_query($connection, "SELECT t.kode_transaksi, t.tanggal, t.no_order, t.total, t.QRIS, t.tunai, t.kembalian, k.nama_kasir FROM transaksi t LEFT JOIN kasir k ON t.id_kasir = k.id_kasir ORDER BY t.tanggal DESC LIMIT 4");

$chart_labels = [];
$chart_totals = [];
$chart_codes = [];
$r_chart = mysqli_query($connection, "SELECT kode_transaksi, tanggal, total FROM transaksi ORDER BY tanggal ASC");
if ($r_chart) {
    while ($row = mysqli_fetch_assoc($r_chart)) {
        $chart_labels[] = date('d M Y, H:i', strtotime($row['tanggal']));
        $chart_totals[] = (float) $row['total'];
        $chart_codes[] = $row['kode_transaksi'];
    }
}
?>

<div class="p-8 space-y-8 max-w-[1600px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
        <div>
            <h1 class="text-4xl font-extrabold text-[#1e293b] tracking-tight">Dashboard Indomaret
            </h1>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-[#e2e8f0]">
            <div class="p-6 transition-all duration-200 hover:bg-[#fafbff] flex flex-col justify-between">
                <div class="flex flex-col">
                    <div class="flex flex-row items-center justify-center gap-5 mb-2">
                        <div
                            class="w-12 h-12 rounded-xl bg-[#f0f4ff] flex items-center justify-center text-[#003399] text-xl">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-black uppercase tracking-wider block">Total Kasir
                                Aktif
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-rows-1 gap-2 justify-center">
                        <span class="text-2xl md:text-3xl font-extrabold text-[#1e293b] text-center mt-1 block">
                            <?= $total_kasir ?>
                        </span>
                        <span class="text-base font-medium text-[#64748b]">Petugas</span>
                    </div>
                </div>

                <div
                    class="mt-4 pt-4 border-t border-[#f1f5f9] flex items-center justify-between text-xs font-semibold text-[#64748b]">
                    <span>Jumlah Kasir</span>
                    <a href="/pages/cashiers/list.php"
                        class="text-[#003399] font-semibold hover:underline flex items-center gap-1">
                        Lihat <i class="fa-solid fa-arrow-right text-[0.65rem]"></i>
                    </a>
                </div>
            </div>

            <div class="p-6 transition-all duration-200 hover:bg-[#fafbff] flex flex-col justify-between">
                <div class="flex flex-row gap-5 items-center justify-center mb-2">
                    <div
                        class="w-12 h-12 rounded-xl bg-[#fffbeb] flex items-center justify-center text-[#d97706] text-xl">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="text-xs font-bold text-black uppercase tracking-wider block">Total Produk
                        Terdaftar</span>
                </div>
                <div class="grid grid-rows-1 gap-2 justify-center">
                    <span class="text-center text-2xl md:text-3xl font-extrabold text-[#1e293b] mt-1 block">
                        <?= $total_produk ?>
                    </span>
                    <span class="text-base font-medium text-[#64748b]">Produk</span>
                </div>

                <div
                    class="mt-4 pt-4 border-t border-[#f1f5f9] flex items-center justify-between text-xs font-semibold text-[#64748b]">
                    <span>Jumlah Produk</span>
                    <a href="/pages/products/list.php"
                        class="text-[#003399] font-semibold hover:underline flex items-center gap-1">
                        Lihat <i class="fa-solid fa-arrow-right text-[0.65rem]"></i>
                    </a>
                </div>
            </div>

            <div class="p-6 transition-all duration-200 hover:bg-[#fafbff] flex flex-col justify-between">
                <div class="flex flex-row gap-5 items-center justify-center mb-2">
                    <div
                        class="w-12 h-12 rounded-xl bg-[#ecfdf5] flex items-center justify-center text-[#059669] text-xl">
                        <i class="fa-solid fa-hand-holding-heart"></i>
                    </div>
                    <span class="text-xs font-bold text-black uppercase tracking-wider block">Total Donasi
                        Terkumpul</span>
                </div>
                <div>
                    <span class="text-2xl md:text-3xl font-extrabold text-[#1e293b] text-center mt-1 block">Rp
                        <?= number_format($total_donasi_terkumpul, 0, ',', '.') ?></span>
                </div>
                <div
                    class="mt-4 pt-4 border-t border-[#f1f5f9] flex items-center justify-between text-xs text-[#64748b]">
                    <span><?= $total_donasi_program ?> Program Aktif</span>
                    <a href="/pages/donations/list.php"
                        class="text-[#003399] font-semibold hover:underline flex items-center gap-1">
                        Lihat <i class="fa-solid fa-arrow-right text-[0.65rem]"></i>
                    </a>
                </div>
            </div>

            <div class="p-6 transition-all duration-200 hover:bg-[#fafbff] flex flex-col justify-between">
                <div class="flex flex-row items-center justify-center gap-5 mb-2">
                    <div
                        class="w-12 h-12 rounded-xl bg-[#faf5ff] flex items-center justify-center text-[#7c3aed] text-xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <span class="text-xs font-bold text-black uppercase tracking-wider block">Total Omset
                        Penjualan</span>
                </div>
                <div>

                    <span class="text-2xl text-center md:text-3xl font-extrabold text-[#1e293b] mt-1 block">Rp
                        <?= number_format($total_omset, 0, ',', '.') ?></span>
                </div>
                <div
                    class="mt-4 pt-4 border-t border-[#f1f5f9] flex items-center justify-between text-xs text-[#64748b]">
                    <span><?= $total_transaksi ?> Transaksi Berhasil</span>
                    <a href="/pages/transactions/list.php"
                        class="text-[#003399] font-semibold hover:underline flex items-center gap-1">
                        Lihat <i class="fa-solid fa-arrow-right text-[0.65rem]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div
        class="bg-white rounded-2xl border border-[#e2e8f0] shadow-[0_4px_12px_rgba(0,0,0,0.08)] overflow-hidden w-full">
        <div
            class="px-7 py-5 border-b border-[#e2e8f0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-[#faf5ff] flex items-center justify-center text-[#7c3aed] text-base">
                    <i class="fa-solid fa-chart-area"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#1e293b]">Grafik Omset Penjualan</h2>
                    <p class="text-xs text-[#64748b]">Pergerakan total omset penjualan dari waktu ke waktu berdasarkan
                        waktu transaksi</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[0.7rem] font-semibold uppercase tracking-wider text-[#94a3b8] block">Akumulasi
                    Omset</span>
                <span class="text-base font-extrabold text-[#1e293b]">Rp
                    <?= number_format($total_omset, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="p-6 w-full">
            <?php if (empty($chart_totals)): ?>
                <div class="h-[320px] flex flex-col items-center justify-center text-[#94a3b8]">
                    <i class="fa-solid fa-chart-line text-4xl text-[#cbd5e1] mb-3"></i>
                    <p class="text-sm font-medium text-[#64748b]">Belum ada data transaksi untuk ditampilkan pada grafik.
                    </p>
                </div>
            <?php else: ?>
                <div class="relative w-full h-[340px]">
                    <canvas id="omsetLineChart"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('omsetLineChart');
        if (!canvas) return;

        const labels = <?= json_encode($chart_labels) ?>;
        const totals = <?= json_encode($chart_totals) ?>;
        const codes = <?= json_encode($chart_codes) ?>;

        const ctx = canvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 340);
        gradient.addColorStop(0, 'rgba(124, 58, 237, 0.22)');
        gradient.addColorStop(1, 'rgba(124, 58, 237, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omset Penjualan (Rp)',
                    data: totals,
                    borderColor: '#7c3aed',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#7c3aed',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#f8fafc',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            title: function (items) {
                                const idx = items[0].dataIndex;
                                return `${codes[idx]} • ${items[0].label}`;
                            },
                            label: function (context) {
                                return 'Omset: Rp ' + Number(context.parsed.y).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: 'Inter, sans-serif',
                                size: 11
                            },
                            maxRotation: 0,
                            autoSkip: true
                        }
                    },
                    y: {
                        beginAtZero: true,
                        border: {
                            display: false
                        },
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: 'Inter, sans-serif',
                                size: 11
                            },
                            callback: function (value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>

</body>

</html>