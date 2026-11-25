document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchContainer = document.getElementById('searchContainer');
    const searchDropdown = document.getElementById('searchDropdown');
    const navItems = document.querySelectorAll('.nav-item');
    const activeMenuText = document.getElementById('activeMenuText');

    // 1. Ctrl + K Shortcut Listener
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchContainer.classList.add('active');
            searchDropdown.classList.add('show');
        }

        // Close dropdown on Escape
        if (e.key === 'Escape') {
            searchInput.blur();
            searchContainer.classList.remove('active');
            searchDropdown.classList.remove('show');
        }
    });

    // 2. Search Input Focus & Blur Handlers
    searchInput.addEventListener('focus', () => {
        searchContainer.classList.add('active');
        searchDropdown.classList.add('show');
    });

    document.addEventListener('click', (e) => {
        if (!searchContainer.contains(e.target)) {
            searchContainer.classList.remove('active');
            searchDropdown.classList.remove('show');
        }
    });

    // 3. Search Filter Simulation
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        const listItems = searchDropdown.querySelectorAll('.dropdown-list li');

        listItems.forEach(item => {
            const title = item.querySelector('.item-title').textContent.toLowerCase();
            const sub = item.querySelector('.item-sub').textContent.toLowerCase();

            if (title.includes(query) || sub.includes(query) || query === '') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });

        searchDropdown.classList.add('show');
    });

    // 4. Navigation Menu Switching (Cashiers, Donations, Products, Transaction)
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();

            // Remove active class from all
            navItems.forEach(nav => nav.classList.remove('active'));

            // Add active class to clicked
            item.classList.add('active');

            // Update demo display text
            const menuName = item.querySelector('span').textContent;
            activeMenuText.textContent = menuName;
        });
    });

    // 5. Dropdown Item Click Action
    const dropdownItems = searchDropdown.querySelectorAll('.dropdown-list li');
    dropdownItems.forEach(li => {
        li.addEventListener('click', () => {
            const title = li.querySelector('.item-title').textContent;
            searchInput.value = title;
            searchDropdown.classList.remove('show');
            searchContainer.classList.remove('active');
        });
    });
});