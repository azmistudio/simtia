@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
    $GridHeight = $InnerHeight - 275 . "px";
    $end_date = !empty($bookyear) ? $bookyear->end_date : date('Y-m-d');
    $old_year = !empty($bookyear) ? $bookyear->book_year : date('Y');
    $new_year = $old_year + 1;
    $new_date = date('Y-m-1', strtotime('first day of next month', strtotime($end_date)));
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Proses Tutup Buku</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div id="page-bookyear-close" class="container-fluid">
            <div class="row">
                <div class="col-12 pt-3">
                    <div>
                        &nbsp;<span><b>Menentukan tahun buku baru, membuat saldo awal untuk tahun buku baru.</b></span><br/>
                        &nbsp;<span style="color: darkred;"><b>PERINGATAN: Tahun buku lama tidak dapat diakses lagi setelah tahun buku baru dibuat.</b></span>
                    </div>
                </div>
                <div class="col-12 pl-3 pt-1">
                    <hr/>
                    <br/>
                    <form id="form-accounting-bookyear-close" method="post">
                        <div class="mb-1">
                            <input class="easyui-textbox" style="width:410px;height:22px;" value="{{ $old_year }}" data-options="label:'Tahun Buku berjalan:',labelWidth:'300px',readonly:'true'" />
                        </div>
                        <div class="mb-1">
                            <input name="close_date" id="AccountingBookYearCloseDate" class="easyui-datebox" style="width:410px;height:22px;" value="{{ $end_date }}" data-options="label:'<b>*</b>Tanggal Tutup Buku:',labelWidth:'300px',formatter:dateFormatter,parser:dateParser" />
                        </div>
                        <div class="mb-1">
                            <input name="book_year" id="AccountingBookYear" class="easyui-numberspinner" style="width:410px;height:22px;" value="{{ $new_year }}" data-options="label:'<b>*</b>Tahun Buku Baru:',labelWidth:'300px',min:{{ $old_year }}" />
                        </div>
                        <div class="mb-1">
                            <input name="start_date" id="AccountingBookYearStartDate" class="easyui-datebox" style="width:410px;height:22px;" value="{{ $new_date }}" data-options="label:'<b>*</b>Tanggal Mulai Buku:',labelWidth:'300px',formatter:dateFormatter,parser:dateParser" />
                        </div>
                        <div class="mb-1">
                            <input name="prefix" class="easyui-textbox" style="width:410px;height:22px;" data-options="label:'<b>*</b>Awalan Kuitansi:',labelWidth:'300px'" />
                        </div>
                        <div class="mb-1">
                            <select name="re_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Kode Akun Laba Ditahan (Retained Earning):',labelWidth:'300px',panelHeight:30">
                                @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->code .' - '. $account->name }}</option>
                                @endforeach
                            </select><br/>
                            <span style="margin-left:300px;"><small><i>Kode akun untuk menampung laba/rugi yang diperoleh tahun berjalan dan menjadi akun Retained Earning (Laba ditahan) untuk tahun buku baru.</i></small></span>
                        </div>
                        <div class="mb-3">
                            <input name="remark" class="easyui-textbox" style="width:550px;height:50px;" data-options="label:'Keterangan:',labelWidth:'300px',multiline:true" />
                        </div>
                        <div style="margin-left:300px;">
                            <button type="submit" class="easyui-linkbutton pl-2 pr-2" data-options="iconCls:'ms-Icon ms-Icon--Save'"><b>Proses Tutup Buku</b></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#form-accounting-bookyear-close").ajaxForm({
        url: "{{ url('finance/book/close/store') }}",
        data: { _token: '{{ csrf_token() }}' },
        beforeSubmit: function(arr, $form, options) {
            $("#page-bookyear-close").waitMe({effect:"facebook"})
        },
        success: function(response) {
            $("#page-bookyear-close").waitMe('hide')
            if (response.success) {
                $("#page-bookyear-close").waitMe({effect:"none"})
                $.messager.alert('Informasi', response.message)
            } else {
                $.messager.alert('Peringatan', response.message, 'error')
            }
        },
        error: function(xhr) {
            failResponse(xhr)
            $("#page-bookyear-close").waitMe('hide')
        }
    })
</script>