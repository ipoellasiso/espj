<script type="text/javascript">
$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#tabelRekanan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('rekanan.index') }}",
        columns: [
            {data: 'DT_RowIndex', orderable:false, searchable:false},
            {data: 'nama_rekanan'},
            {data: 'alamat'},
            {data: 'npwp'},
            {data: 'unit'},
            {data: 'action', orderable:false, searchable:false},
        ]
    });

    // ✅ TOMBOL TAMBAH
    $('#createRekanan').click(function(){
        $('#formRekanan')[0].reset();
        // 🔥 FORCE CLEAR SEMUA FIELD
        $('#id').val('');
        $('#nama_rekanan').val('');
        $('#alamat').val('');
        $('#npwp').val('');
        $('#jabatan').val('');
        $('#nip').val('');
        $('#modalRekanan').modal('show');
    });

    // ✅ EDIT
    $('body').on('click','.editRekanan',function(){

        let id = $(this).data('id');

        $.get('/rekanan/edit/' + id, function(res){

            $('#modalRekanan').modal('show');

            $('#id').val(res.id);
            $('#nama_rekanan').val(res.nama_rekanan);
            $('#alamat').val(res.alamat);
            $('#npwp').val(res.npwp);
            $('#jabatan').val(res.jabatan);
            $('#nip').val(res.nip);
        });
    });

    // ✅ SIMPAN
    $('#formRekanan').submit(function(e){
        e.preventDefault();

        $.post('/rekanan/store', $(this).serialize(), function(res){

            if(res.success === false){
                Swal.fire('Error', res.message, 'error');
                return;
            }

            Swal.fire('Berhasil', res.success, 'success');

            $('#modalRekanan').modal('hide');
            table.draw();
        });
    });

    // ✅ DELETE
    $('body').on('click','.deleteRekanan',function(){

        let id = $(this).data('id');

        Swal.fire({
            title:'Hapus data rekanan?',
            icon:'warning',
            showCancelButton:true,
        }).then(r=>{
            if(r.isConfirmed){

                $.ajax({
                    url:'/rekanan/destroy/' + id,
                    type:'DELETE',

                    success:function(res){
                        Swal.fire('OK',res.success,'success');
                        table.draw();
                    }
                });
            }
        });
    });

});
</script>
