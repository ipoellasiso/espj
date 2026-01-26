    <script type="text/javascript">
        $(function () {

        /*------------------------------------------
        --------------------------------------------
        Pass Header Token
        --------------------------------------------
        --------------------------------------------*/
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /*------------------------------------------
        --------------------------------------------
        Render DataTable
        --------------------------------------------
        --------------------------------------------*/
        // === Datatable ===
        // === Render DataTable ===
        var table = $('#tabelUnit').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('unit-organisasi.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', className:'text-center'},
                {data: 'kode', name: 'kode'},
                {data: 'nama', name: 'nama'},
                {data: 'nama_bidang', name: 'nama_bidang'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className:'text-center'},
            ]
        });

        // === Tambah Data ===
        $('#createUnit').click(function(){
            $('#formUnit').trigger("reset");
            $('#id').val('');
            $('#saveBtn').val("create-unit");
            $('#modalUnit').modal('show');
        });

        // === Edit Data ===
        $('body').on('click', '.editUnit', function () {
            var id = $(this).data('id');
            $.get("/unit-organisasi/edit/"+id, function (data) {
                $('#modalUnit').modal('show');
                $('#id').val(data.id);
                $('#kode').val(data.kode);
                $('#nama').val(data.nama);
                $('#id_bidang').val(data.id_bidang);
                $('#kepala').val(data.kepala);
                $('#nip_kepala').val(data.nip_kepala);
                $('#bendahara').val(data.bendahara);
                $('#nip_bendahara').val(data.nip_bendahara);
                $('#ppk').val(data.ppk);
                $('#nip_ppk').val(data.nip_ppk);
                $('#alamat').val(data.alamat);
                $('#pejabatbarang').val(data.pejabatbarang);
                $('#nip_pejabatbarang').val(data.nip_pejabatbarang);
                $('#nomor_sk').val(data.nomor_sk);
                $('#tanggal_sk').val(data.tanggal_sk);
                $('#bend_barang').val(data.bend_barang);
                $('#nip_bend_barang').val(data.nip_bend_barang);
                $('#ket').val(data.ket);
            })
        });

        // === Simpan Data ===
        $('body').on('submit', '#formUnit', function(e){
            e.preventDefault();
            $('#saveBtn').html('Menyimpan...');

            $.ajax({
                type: 'POST',
                url: "/unit-organisasi/store",
                data: $(this).serialize(),
                success: function(res){
                    $('#formUnit').trigger("reset");
                    $('#modalUnit').modal('hide');
                    $('#saveBtn').html('Simpan');
                    Swal.fire('Berhasil!', res.success, 'success');
                    table.draw();
                },
                error: function(){
                    $('#saveBtn').html('Simpan');
                    Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data.', 'error');
                }
            });
        });

        // === Hapus Data ===
        $('body').on('click', '.deleteUnit', function () {
            var id = $(this).data("id");

            Swal.fire({
                title: 'Hapus Data?',
                text: "Data akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "/unit-organisasi/destroy/"+id,
                        success: function(res){
                            Swal.fire('Berhasil!', res.success, 'success');
                            table.draw();
                        }
                    });
                }
            });
        });

        // === Import Excel ===
        $('#importExcelBtn').click(function(){
            $('#formImportUnit').trigger("reset");
            $('#modalImportUnit').modal('show');
        });

        $('#formImportUnit').on('submit', function(e){
            e.preventDefault();
            var formData = new FormData(this);
            $('#btnImport').html('Mengimpor...');

            $.ajax({
                type: 'POST',
                url: "{{ route('unit-organisasi.import') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res){
                    $('#btnImport').html('Import');
                    $('#modalImportUnit').modal('hide');
                    if(res.success){
                        Swal.fire('Berhasil!', res.success, 'success');
                    } else {
                        Swal.fire('Gagal!', res.error, 'error');
                    }
                    table.ajax.reload();
                },
                error: function(){
                    $('#btnImport').html('Import');
                    Swal.fire('Error!', 'File tidak valid atau gagal diproses.', 'error');
                }
            });
        });

    });
    </script>   