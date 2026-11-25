<?php
$alertEntity = $alertEntity ?? 'data';
$alertDeleteUrl = $alertDeleteUrl ?? '';
?>
<script>
    function confirmDelete(event, id, name, deleteUrl = <?= json_encode($alertDeleteUrl) ?>) {
        event.preventDefault();
        const entity = <?= json_encode($alertEntity) ?>;
        const targetUrl = deleteUrl
            ? `${deleteUrl}${encodeURIComponent(id)}`
            : `/process/cashiers_process.php?action=delete&id=${encodeURIComponent(id)}`;

        Swal.fire({
            title: `Hapus ${entity.charAt(0).toUpperCase() + entity.slice(1)}?`,
            html: `Apakah Anda yakin ingin menghapus data ${entity} <b>${name}</b> (ID: ${id})?<br><span class="text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-4 py-2 font-semibold',
                cancelButton: 'rounded-lg px-4 py-2 font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = targetUrl;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const entity = <?= json_encode($alertEntity) ?>;
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status) {
            if (status === 'success_delete') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus!',
                    text: `Data ${entity} telah berhasil dihapus dari sistem.`,
                    confirmButtonColor: '#003399',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold'
                    }
                });
            } else if (status === 'success_add' || status === 'success_create') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Ditambahkan!',
                    text: `Data ${entity} baru berhasil disimpan.`,
                    confirmButtonColor: '#003399',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold'
                    }
                });
            } else if (status === 'success_edit') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Diperbarui!',
                    text: `Data ${entity} berhasil diperbarui.`,
                    confirmButtonColor: '#003399',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold'
                    }
                });
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: message ? decodeURIComponent(message) : `Gagal memproses data ${entity}.`,
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg px-4 py-2 font-semibold'
                    }
                });
            }
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>
