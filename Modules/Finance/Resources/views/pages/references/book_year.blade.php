@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
    $GridHeight = $InnerHeight - 275 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Tahun Buku</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportBookYear('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-book-year" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <input id="fname-book-year" class="easyui-numberspinner" style="width:285px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:100,min:2021">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterBookYear({fbookyear: $('#fname-book-year').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-book-year').form('reset');filterBookYear({})">Batal</a>
                </div>
            </form>
            <table id="tb-book-year" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100],
                    rowStyler:function (index, row) { if (row.is_active === 'Aktif') { return 'font-weight:600' } }">
                <thead>
                    <tr>
                        <th data-options="field:'book_year',width:100,resizeable:true,sortable:true,align:'center'">Tahun Buku</th>
                        <th data-options="field:'period',width:160,resizeable:true">Periode</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-book-year" class="panel-top">
            <a id="newBookYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newBookYear()">Baru</a>
            <a id="editBookYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editBookYear()">Ubah</a>
            <a id="saveBookYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveBookYear()">Simpan</a>
            <a id="clearBookYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearBookYear()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-book-year"></span>Tahun Buku: <span id="title-book-year"></span></h6>
        </div>
        <div id="page-book-year" class="pt-3 pb-3">
            <form id="form-book-year-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-book-year" name="id" value="-1" />
                            <input type="hidden" id="id-book-year-start" name="start_from" value="" />
                            <div class="mb-1">
                                <input name="book_year" id="AccountingBookYear" class="easyui-numberspinner" style="width:240px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'125px',min:2022" />
                            </div>
                            <div class="mb-1">
                                <input name="start_date" id="AccountingBookYearStart" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal Mulai:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <input name="prefix" class="easyui-textbox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Awalan Kuitansi:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:335px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="is_active" class="easyui-checkbox" value="0" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before',readonly:'true'" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionBookYear = document.getElementById("menu-act-book-year").getElementsByTagName("a")
    var titleBookYear = document.getElementById("title-book-year")
    var markBookYear = document.getElementById("mark-book-year")
    var idBookYear = document.getElementById("id-book-year")
    var dgBookYear = $("#tb-book-year")
    $(function () {
        sessionStorage.formTahun_Buku = "init"
        dgBookYear.datagrid({
            url: "{{ url('finance/book/year/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTahun_Buku == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleBookYear.innerText = row.book_year
                    actionButtonBookYear("active",[2,3])
                    $("#form-book-year-main").form("load", "{{ url('finance/book/year/show') }}" + "/" + row.id)
                    $("#AccountingBookYear").numberspinner("readonly", true)
                    $("#AccountingBookYearStart").datebox("readonly", true)
                    $("#page-book-year").waitMe("hide")
                }
            }
        })
        dgBookYear.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgBookYear.datagrid('getPager').pagination())
        actionButtonBookYear("{{ $ViewType }}", [])
        $("#AccountingBookYear").numberspinner({
            onChange: function(value){
                titleBookYear.innerText = value
            }
        })
        $("#page-book-year").waitMe({effect:"none"})
    })
    function filterBookYear(params) {
        if (Object.keys(params).length > 0) {
            dgBookYear.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgBookYear.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newBookYear() {
        sessionStorage.formTahun_Buku = "active"
        $("#form-book-year-main").form("reset")
        actionButtonBookYear("active", [0,1])
        markBookYear.innerText = "*"
        titleBookYear.innerText = ""
        idBookYear.value = "-1"
        $("#id-book-year-start").val("")
        $("#AccountingBookYear").textbox('textbox').focus()
        $("#AccountingBookYear").numberspinner("readonly", false)
        $("#AccountingBookYearStart").datebox("readonly", false)
        $("#page-book-year").waitMe("hide")
    }
    function editBookYear() {
        sessionStorage.formTahun_Buku = "active"
        markBookYear.innerText = "*"
        actionButtonBookYear("active", [0,1])
    }
    function saveBookYear() {
        if (sessionStorage.formTahun_Buku == "active") {
            ajaxBookYear("finance/book/year/store")
        }
    }
    function ajaxBookYear(route) {
        $("#form-book-year-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-book-year").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-book-year").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-book-year").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearBookYear()
            $("#tb-book-year").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearBookYear() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearBookYear()
            }
        })
    }
    function actionButtonBookYear(viewType, idxArray) {
        for (var i = 0; i < menuActionBookYear.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionBookYear[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionBookYear[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionBookYear[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionBookYear[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearBookYear() {
        sessionStorage.formTahun_Buku = "init"
        $("#form-book-year-main").form("reset")
        actionButtonBookYear("init", [])
        titleBookYear.innerText = ""
        markBookYear.innerText = ""
        idBookYear.value = "-1"
        $("#AccountingBookYear").numberspinner("readonly", false)
        $("#AccountingBookYearStart").datebox("readonly", false)
        $("#page-book-year").waitMe({effect:"none"})
    }
    function exportBookYear(document) {
        var dg = $("#tb-book-year").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('finance/book/year/export-') }}" + document,dg.rows,"Ekspor data Tahun Buku ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>