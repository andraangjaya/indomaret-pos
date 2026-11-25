<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indomaret POS</title>
    <link rel="stylesheet" href="/assets/css/output.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-[#f4f6f9] text-[#1e293b] min-h-screen font-[Inter,sans-serif]">
    <header
        class="bg-white h-18 px-7 flex items-center justify-between border-b-2 border-[#e2e8f0] shadow-sm sticky top-0 z-[1000]">
        <div class="flex items-center min-w-[180px]">
            <a href="/pages/dashboard.php"
                class="flex items-center text-[1.35rem] font-extrabold tracking-tight cursor-pointer no-underline">
                <span class="text-[#003399]">Indo</span><span class="text-[#E30613]">maret</span>
            </a>
        </div>

        <div class="flex items-center gap-6">
            <nav class="flex items-center gap-4">
                <a href="/pages/dashboard.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg no-underline text-[#64748b] text-[0.88rem] font-semibold transition-all duration-200 hover:text-[#003399] hover:bg-[#f0f4ff]"
                    data-menu="dashboard">
                    <i class="fa-solid fa-gauge-high text-[0.95rem]"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/pages/cashiers/list.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg no-underline text-[#64748b] text-[0.88rem] font-semibold transition-all duration-200 hover:text-[#003399] hover:bg-[#f0f4ff]"
                    data-menu="cashiers">
                    <i class="fa-solid fa-cash-register text-[0.95rem]"></i>
                    <span>Cashiers</span>
                </a>
                <a href="/pages/donations/list.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg no-underline text-[#64748b] text-[0.88rem] font-semibold transition-all duration-200 hover:text-[#003399] hover:bg-[#f0f4ff]"
                    data-menu="donations">
                    <i class="fa-solid fa-hand-holding-heart text-[0.95rem]"></i>
                    <span>Donations</span>
                </a>
                <a href="/pages/products/list.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg no-underline text-[#64748b] text-[0.88rem] font-semibold transition-all duration-200 hover:text-[#003399] hover:bg-[#f0f4ff]"
                    data-menu="products">
                    <i class="fa-solid fa-boxes-stacked text-[0.95rem]"></i>
                    <span>Products</span>
                </a>
                <a href="/pages/transactions/list.php"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg no-underline text-[#64748b] text-[0.88rem] font-semibold transition-all duration-200 hover:text-[#003399] hover:bg-[#f0f4ff]"
                    data-menu="transaction">
                    <i class="fa-solid fa-receipt text-[0.95rem]"></i>
                    <span>Transaction</span>
                </a>
            </nav>
        </div>
    </header>
</body>
<script src="/assets/js/script.js"></script>