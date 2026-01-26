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

        let table = $('#tabelPptk').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('pptk.index') }}",
            columns: [
                {data: 'DT_RowIndex', orderable:false, searchable:false},
                {data: 'nama'},
                {data: 'nip'},
                {data: 'jabatan'},
                {data: 'unit'},
                {data: 'action', orderable:false, searchable:false},
            ]
        });

        $('#createPptk').click(function(){
            $('#formPptk')[0].reset();
            $('#id').val('');
            $('#modalPptk').modal('show');
        });

        $('body').on('click','.editPptk',function(){
            let id = $(this).data('id');
            $.get('/pptk/edit/'+id, function(res){
                $('#modalPptk').modal('show');
                $('#id').val(res.id);
                $('#id_unit').val(res.id_unit);
                $('#nama').val(res.nama);
                $('#nip').val(res.nip);
                $('#jabatan').val(res.jabatan);
            });
        });

        $('#formPptk').submit(function(e){
            e.preventDefault();
            $.post('/pptk/store', $(this).serialize(), function(res){
                Swal.fire('Berhasil', res.success, 'success');
                $('#modalPptk').modal('hide');
                table.draw();
            });
        });

        $('body').on('click','.deletePptk',function(){
            let id = $(this).data('id');
            Swal.fire({
                title:'Hapus?',
                showCancelButton:true
            }).then(r=>{
                if(r.isConfirmed){
                    $.ajax({
                        url:'/pptk/destroy/'+id,
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