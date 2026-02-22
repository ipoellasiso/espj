<script type="text/javascript">
$(function () {

    let table = $('#tabelRegisterPajak').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('pajak.register') }}",
            data: function (d) {
                d.tgl_awal  = $('#tgl_awal').val();
                d.tgl_akhir = $('#tgl_akhir').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', orderable:false, searchable:false},
            {data: 'nomor_spj'},
            {data: 'tanggal'},
            {data: 'npwp'},
            {data: 'jenis_pajak'},
            {data: 'nilai_pajak', name: 'nilai_pajak', className:'text-end'},
            {data: 'ebilling'},
            {data: 'ntpn'},
        ]
    });

    // ✅ FILTER
    $('#filterPajak').click(function(){
        table.draw();
    });

    // ✅ EXPORT EXCEL
    $('#exportExcel').click(function(){

        let awal  = $('#tgl_awal').val();
        let akhir = $('#tgl_akhir').val();

        window.location.href =
            `/pajak/register/export?tgl_awal=${awal}&tgl_akhir=${akhir}`;
    });

});
</script>