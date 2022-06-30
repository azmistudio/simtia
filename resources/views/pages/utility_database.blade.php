@php
    $WindowHeight = $InnerHeight - 142 . "px";
    $WindowWidth = $InnerWidth - 73 . "px";
    $ContentHeight = $InnerHeight - 310 . "px";
    $ViewType = $ViewType;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Pengaturan Basis Data</h5>
        </div>
        <div class="col-4 p-0 text-right">
            
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:30%;">
        <div class="title">
            <h6>Buat Cadangan (Backup)</h6>
        </div>
    </div>
    <div data-options="region:'center'" style="width:30%;">
        <div class="title">
            <h6>Kembalikan Cadangan (Restore)</h6>
        </div>
    </div>
    <div data-options="region:'east'" style="width:40%;">
        <div class="title">
            <h6>Atur Ulang (Reset)</h6>
        </div>
        <div class="p-3" id="DatabaseReset">
            <div class="well-warning mb-3">
                <ul class="mb-0" style="padding-left: 24px;">
                    <li><b>Berfungsi untuk menghapus seluruh data yang ada di dalam basis data, sesuai dengan Modul yang dipilih dan atau Semua Modul.</b></li>
                    <li><b>Jika ingin menghapus data per modul, silahkan mulai dari modul terbawah, sesuai dengan hirarki data.</b></li>
                </ul>
            </div>
            <div>
                <table id="tree-db-modules" style="width:100%;height:{{ $ContentHeight }}" class="easyui-treegrid" title="Daftar Modul" data-options="url:'{{ url('utility/database/module') }}',method:'get',lines: true,animate:true,idField:'id',treeField:'name'">
                    <thead>
                        <tr>
                            <th data-options="field:'name'" width="300">Modul</th>
                            <th data-options="field:'action',align:'center'" width="100">Aksi</th>
                        </tr>  
                    </thead>                                  
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        
    })
    function resetAllModule() {

    }
    function resetAccountingModule() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus seluruh data Modul Keuangan, tetap lanjutkan?", function (r) {
            if (r) {
                ajaxResetModule("accounting")
            }
        })
    }
    function resetAcademicModule() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus seluruh data Modul Akademik, tetap lanjutkan?", function (r) {
            if (r) {
                ajaxResetModule("academic")
            }
        })
    }
    function resetMasterDataModule() {

    }
    function ajaxResetModule(param) {
        $("#DatabaseReset").waitMe({effect: "facebook"})
        $.post("{{ url('utility/database/module/destroy') }}", $.param({_token: "{{ csrf_token() }}", module: param}, true), function(response) {
            if (response.success) {
                $.messager.alert("Informasi", response.message)
            } else {
                $.messager.alert("Peringatan", response.message, "error")
            }
            $("#DatabaseReset").waitMe("hide")
        })
    }
</script>