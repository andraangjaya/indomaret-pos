<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indomaret POS Header Navbar</title>
    <!-- Link to Separate CSS File -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- FontAwesome for POS Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- POS Header Navbar -->
    <header class="pos-header">

        <!-- Left Section: Indomaret POS Branding -->
        <div class="header-left">
            <div class="brand-logo">
                <span class="brand-indro">Indo</span><span class="brand-maret">maret</span>
                <span class="pos-badge">POS</span>
            </div>
        </div>

        <!-- Middle Section: Ctrl + K Search Bar -->
        <div class="header-middle">
            <div class="search-container" id="searchContainer">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search products, SKUs, or receipts..."
                    autocomplete="off">
                <div class="search-shortcut">
                    <kbd>Ctrl</kbd> + <kbd>K</kbd>
                </div>

                <!-- Search Dropdown Results -->
                <div class="search-dropdown" id="searchDropdown">
                    <div class="dropdown-header">Quick Navigation & Results</div>
                    <ul class="dropdown-list">
                        <li data-action="product">
                            <i class="fa-solid fa-box-open"></i>
                            <div>
                                <span class="item-title">Indocafe Coffeemix 3in1 20g</span>
                                <span class="item-sub">SKU: 8991002345 - Rp 1.500</span>
                            </div>
                        </li>
                        <li data-action="product">
                            <i class="fa-solid fa-box-open"></i>
                            <div>
                                <span class="item-title">Ultramilk Chocolate 250ml</span>
                                <span class="item-sub">SKU: 8992004561 - Rp 6.500</span>
                            </div>
                        </li>
                        <li data-action="transaction">
                            <i class="fa-solid fa-receipt"></i>
                            <div>
                                <span class="item-title">View Recent Transactions</span>
                                <span class="item-sub">Check today's sales log</span>
                            </div>
                        </li>
                        <li data-action="donation">
                            <i class="fa-solid fa-hand-holding-heart"></i>
                            <div>
                                <span class="item-title">Donation Summary (Donasi Kemanusiaan)</span>
                                <span class="item-sub">Total collected today: Rp 145.000</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Section: 4 Menu Items + Cashier Profile -->
        <div class="header-right">
            <nav class="nav-menu">
                <a href="/pages/cashiers/add.php" class="nav-item active" data-menu="cashiers">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Cashiers</span>
                </a>
                <a href="#donations" class="nav-item" data-menu="donations">
                    <i class="fa-solid fa-hand-holding-heart"></i>
                    <span>Donations</span>
                </a>
                <a href="#products" class="nav-item" data-menu="products">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Products</span>
                </a>
                <a href="#transaction" class="nav-item" data-menu="transaction">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Transaction</span>
                </a>
            </nav>

            <div class="user-profile">
                <div class="avatar">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="user-info">
                    <span class="user-name">Budi Santoso</span>
                    <span class="user-role">Kasir #04</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Link to Separate JS File -->
    <script src="script.js"></script>
</body>

</html>