<script type="text/javascript">
$(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(document).on('click', '.btn-hapus-lpj', function() {

        let id  = $(this).data('id');
        let btn = $(this);

        Swal.fire({
            title: 'Hapus LPJ?',
            text: "Data LPJ akan dihapus permanen",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/lpj/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    success: function(res) {

                        if (res.success) {

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1200,
                                customClass: {
                                    popup: 'colored-toast'
                                }
                            });

                            /* 🔥 HILANGKAN BARIS */
                            btn.closest('tr').fadeOut(400);
                        }
                        else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },

                    error: function(xhr) {

                        Swal.fire(
                            'Error',
                            xhr.responseJSON?.message ?? 'Tidak bisa menghapus LPJ',
                            'error'
                        );
                    }
                });

            }
        });
    });

});
</script>
