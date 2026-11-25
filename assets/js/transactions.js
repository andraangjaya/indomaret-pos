const VARIANT_CONFIG = {
    add: {
        emptyColClass: 'px-6 py-12',
        emptyIcon: 'fa-basket-shopping',
        emptyText: 'Belum ada produk yang dimasukkan ke transaksi ini.',
        indexCellClass: 'px-6 py-4 text-center text-xs font-semibold text-[#64748b]',
        hargaCellClass: 'px-6 py-4 text-right text-[#64748b]',
        diskonBadgeClass: 'px-2 py-0.5 font-bold text-black',
        diskonEmptyClass: 'text-black',
        subtotalCellClass: 'px-6 py-4 text-right font-bold text-black',
        qtyUnit: 'pcs',
        wrapProductName: false
    },
    edit: {
        emptyColClass: 'px-6 py-10',
        emptyIcon: 'fa-box-open',
        emptyText: 'Belum ada item dalam transaksi ini.',
        indexCellClass: 'px-6 py-4 text-center text-black font-bold',
        hargaCellClass: 'px-6 py-4 text-right text-black font-bold',
        diskonBadgeClass: 'inline-flex items-center px-2 py-0.5 text-black font-bold',
        diskonEmptyClass: 'text-[#cbd5e1]',
        subtotalCellClass: 'px-6 py-4 text-right text-black font-bold',
        qtyUnit: 'item',
        wrapProductName: true
    }
};

let cart = [];
let currentPaymentMethod = 'tunai';
let txVariant = 'add';

function initTransactionState() {
    const config = window.transactionConfig || {};
    cart = Array.isArray(config.cart) ? config.cart : [];
    currentPaymentMethod = config.paymentMethod || 'tunai';
    txVariant = VARIANT_CONFIG[config.variant] ? config.variant : 'add';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function syncPickerData() {
    const picker = document.getElementById('pickerProduct');
    const selected = picker.options[picker.selectedIndex];
    const harga = selected.getAttribute('data-harga') || 0;
    const diskon = selected.getAttribute('data-diskon') || 0;

    document.getElementById('pickerHarga').value = harga;
    document.getElementById('pickerDiskon').value = Math.round(diskon);
}

function addProductToCart() {
    const picker = document.getElementById('pickerProduct');
    const selected = picker.options[picker.selectedIndex];
    const id = picker.value;

    if (!id) {
        Swal.fire({
            icon: 'warning',
            title: 'Produk Belum Dipilih',
            text: 'Silakan pilih produk terlebih dahulu.',
            confirmButtonColor: '#003399'
        });
        return;
    }

    const nama = selected.getAttribute('data-nama');
    const harga = parseFloat(selected.getAttribute('data-harga')) || 0;
    const diskon = parseFloat(selected.getAttribute('data-diskon')) || 0;
    const qty = Math.max(1, parseInt(document.getElementById('pickerQty').value) || 1);

    const existingIndex = cart.findIndex(item => item.id === id);
    if (existingIndex > -1) {
        cart[existingIndex].qty += qty;
    } else {
        cart.push({ id, nama, harga, diskon, qty });
    }

    picker.value = '';
    document.getElementById('pickerHarga').value = '';
    document.getElementById('pickerDiskon').value = '';
    document.getElementById('pickerQty').value = 1;

    renderCart();
}

function updateCartQty(index, delta) {
    const nextQty = cart[index].qty + delta;
    if (nextQty >= 1) {
        cart[index].qty = nextQty;
        renderCart();
    }
}

function setCartQty(index, val) {
    const parsed = parseInt(val);
    cart[index].qty = (isNaN(parsed) || parsed < 1) ? 1 : parsed;
    renderCart();
}

function removeCartItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function renderCart() {
    const tbody = document.getElementById('cartTableBody');
    const cfg = VARIANT_CONFIG[txVariant] || VARIANT_CONFIG.add;
    tbody.innerHTML = '';

    if (cart.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="7" class="${cfg.emptyColClass} text-center text-[#94a3b8]">
                    <i class="fa-solid ${cfg.emptyIcon} text-3xl mb-2 text-[#cbd5e1] block"></i>
                    <span class="block font-medium text-xs text-[#64748b]">${cfg.emptyText}</span>
                </td>
            </tr>
        `;
    } else {
        cart.forEach((item, index) => {
            const subtotal = (item.harga - item.diskon) * item.qty;
            const safeNama = escapeHtml(item.nama);
            const nameHtml = cfg.wrapProductName
                ? `<div><span class="block text-black font-bold">${safeNama}</span></div>`
                : safeNama;
            const diskonHtml = item.diskon > 0
                ? `<span class="${cfg.diskonBadgeClass}">-Rp ${(item.diskon * item.qty).toLocaleString('id-ID')}</span>`
                : `<span class="${cfg.diskonEmptyClass}">-</span>`;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-[#fafbff] transition-colors';
            tr.innerHTML = `
                <td class="${cfg.indexCellClass}">
                    ${index + 1}
                    <input type="hidden" name="id_produk[]" value="${escapeHtml(item.id)}">
                    <input type="hidden" name="harga[]" value="${item.harga}">
                    <input type="hidden" name="kuantitas[]" value="${item.qty}">
                    <input type="hidden" name="diskon[]" value="${item.diskon}">
                </td>
                <td class="px-6 py-4 font-semibold text-[#1e293b]">${nameHtml}</td>
                <td class="${cfg.hargaCellClass}">Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td class="px-6 py-4 text-center">
                    <div class="inline-flex items-center border border-[#e2e8f0] rounded-lg bg-white overflow-hidden">
                        <button type="button" onclick="updateCartQty(${index}, -1)"
                            class="w-7 h-7 flex items-center justify-center text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1e293b] cursor-pointer font-bold">-</button>
                        <input type="number" min="1" value="${item.qty}" onchange="setCartQty(${index}, this.value)"
                            class="w-10 text-center text-xs font-bold text-[#1e293b] outline-none border-x border-[#e2e8f0] py-1 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <button type="button" onclick="updateCartQty(${index}, 1)"
                            class="w-7 h-7 flex items-center justify-center text-[#64748b] hover:bg-[#f1f5f9] hover:text-[#1e293b] cursor-pointer font-bold">+</button>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">${diskonHtml}</td>
                <td class="${cfg.subtotalCellClass}">Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td class="px-6 py-4 text-center">
                    <button type="button" onclick="removeCartItem(${index})"
                        class="w-7 h-7 rounded-lg border border-red-200 bg-red-50 text-red-500 hover:bg-red-100 transition inline-flex items-center justify-center cursor-pointer">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    calculateCart();
}

function togglePaymentMethod(method) {
    currentPaymentMethod = method;
    const tunaiSection = document.getElementById('tunaiSection');
    const qrisSection = document.getElementById('qrisSection');
    const badge = document.getElementById('headerPaymentBadge');
    const total = parseFloat(document.getElementById('inputTotal').value) || 0;

    if (method === 'qris') {
        tunaiSection.classList.add('hidden');
        qrisSection.classList.remove('hidden');
        if (badge) {
            badge.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200"><i class="fa-solid fa-qrcode text-[0.7rem]"></i> QRIS</span>`;
        }
        document.getElementById('tunai').value = 0;
        document.getElementById('qris').value = total;
        document.getElementById('inputKembalian').value = 0;
        document.getElementById('labelKembalian').innerText = 'Rp 0';
    } else {
        qrisSection.classList.add('hidden');
        tunaiSection.classList.remove('hidden');
        if (badge) {
            badge.innerHTML = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"><i class="fa-solid fa-money-bill-wave text-[0.7rem]"></i> TUNAI</span>`;
        }
        document.getElementById('qris').value = 0;
        calculateChange();
    }
}

function calculateCart() {
    const cfg = VARIANT_CONFIG[txVariant] || VARIANT_CONFIG.add;
    let subtotalItems = 0;
    let totalQty = 0;

    cart.forEach(item => {
        subtotalItems += (item.harga - item.diskon) * item.qty;
        totalQty += item.qty;
    });

    const donasiSelect = document.getElementById('id_donasi');
    const selectedDonasi = donasiSelect.options[donasiSelect.selectedIndex];
    const hargaDonasi = parseFloat(selectedDonasi.getAttribute('data-harga')) || 0;
    const namaDonasi = selectedDonasi.value ? selectedDonasi.text.split('(+')[0].trim() : 'Donasi';

    const grandTotal = subtotalItems + hargaDonasi;

    const badgeItemCount = document.getElementById('badgeItemCount');
    if (badgeItemCount) {
        badgeItemCount.innerText = `${cart.length} Produk (${totalQty} pcs)`;
    }

    const readonlyTotalItem = document.getElementById('readonlyTotalItem');
    if (readonlyTotalItem) {
        readonlyTotalItem.value = `${cart.length} Produk`;
    }

    const receiptItemQty = document.getElementById('receiptItemQty');
    if (receiptItemQty) {
        receiptItemQty.innerText = totalQty;
    }

    const labelDonasiTitle = document.getElementById('labelDonasiTitle');
    if (labelDonasiTitle) {
        labelDonasiTitle.innerText = selectedDonasi.value ? `Donasi (${namaDonasi})` : 'Donasi';
    }

    document.getElementById('summaryQty').innerText = `${totalQty} ${cfg.qtyUnit}`;
    document.getElementById('summaryItemSubtotal').innerText = `Rp ${subtotalItems.toLocaleString('id-ID')}`;
    document.getElementById('labelSubtotal').innerText = `Rp ${subtotalItems.toLocaleString('id-ID')}`;
    document.getElementById('labelDonasi').innerText = `+Rp ${hargaDonasi.toLocaleString('id-ID')}`;
    document.getElementById('displayTotal').innerText = `Rp ${grandTotal.toLocaleString('id-ID')}`;
    document.getElementById('inputTotal').value = grandTotal;

    if (currentPaymentMethod === 'qris') {
        document.getElementById('qris').value = grandTotal;
        document.getElementById('tunai').value = 0;
        document.getElementById('inputKembalian').value = 0;
    } else {
        calculateChange();
    }
}

function calculateChange() {
    if (currentPaymentMethod !== 'tunai') return;
    const total = parseFloat(document.getElementById('inputTotal').value) || 0;
    const tunai = parseFloat(document.getElementById('tunai').value) || 0;
    const kembalian = tunai > total ? tunai - total : 0;

    document.getElementById('inputKembalian').value = kembalian;
    document.getElementById('labelKembalian').innerText = `Rp ${kembalian.toLocaleString('id-ID')}`;
}

function setUangPas() {
    const total = parseFloat(document.getElementById('inputTotal').value) || 0;
    document.getElementById('tunai').value = total;
    calculateChange();
}

initTransactionState();

document.addEventListener('DOMContentLoaded', function () {
    initTransactionState();
    const config = window.transactionConfig || {};
    if (config.renderOnLoad) {
        renderCart();
    }
});
