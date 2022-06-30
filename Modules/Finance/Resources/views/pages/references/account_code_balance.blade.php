<div class="container-fluid mt-2 mb-2">
    <div class="row">
        <div class="col-12">
            <form id="form-accounting-code-balance" method="post">
                <input type="hidden" name="bookyear_id" id="id-accounting-code-bookyear" value="{{ $bookyear->id }}" />
                <div class="mb-3">
                    <ul class="well">
                        <li>Set saldo awal akun digunakan untuk mengisikan nilai awal kode akun. Anda dapat mengubah data saldo awal akun selama belum melakukan tutup buku.</li>
                        <li>Tanggal set saldo awal adalah tanggal untuk jurnal saldo awal yaitu akhir bulan sebelum periode bulan saat ini. Contoh : jika Anda memulai buku tanggal 1 Juni 2021, maka tanggal saldo awal adalah 31 Mei 2021.</li>
                    </ul>
                </div>
                <div class="mb-1">
                    <input name="book_year" id="AccountingCodeBalanceBook" class="easyui-textbox" style="width:160px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'100px',readonly:'true'" value="{{ $bookyear->book_year }}" />
                    <span class="mr-2"></span>
                    <input id="AccountingCodeBalancePeriod" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Periode Tanggal:',labelWidth:'130px',readonly:'true'" value="{{ $period }}" />
                    <span class="mr-2"></span>
                    <input name="start_date" id="AccountingCodeBalanceStartDate" class="easyui-datebox" style="width:290px;height:22px;" data-options="label:'Tanggal Set Saldo Awal:',labelWidth:'170px',readonly:'true',formatter:dateFormatter,parser:dateParser" value="{{ $balance_date }}" />
                </div>
            </form>
            <br/>
        </div>
        <div class="col-6">
            <table id="tb-accounting-code-balance-activa" class="easyui-datagrid" style="width:100%;height:310px" 
                data-options="
                    singleSelect:true,
                    method:'post',
                    rownumbers:'true',
                    showFooter:true,
                    title:'Harta | Debit (+), Kredit (-)',
                ">
                <thead>
                    <tr>
                        <th data-options="field:'id',width:50,hidden:'true'">ID</th>
                        <th data-options="field:'pos',width:50,hidden:'true'">POS</th>
                        <th data-options="field:'code',width:100,align:'center'">Kode Akun</th>
                        <th data-options="field:'name',width:280">Nama Akun</th>
                        <th data-options="
                            field:'total',
                            width:120,
                            align:'right',
                            editor:{type:'numberbox',options:{precision:2}},
                            formatter:function(value,row){return calculateTotalCodeBalance(value)}">Jumlah (+/-)</th>
                    </tr>
                </thead>
            </table> 
        </div>
        <div class="col-6">
            <table id="tb-accounting-code-balance-passiva" class="easyui-datagrid" style="width:100%;height:310px" 
                data-options="
                    singleSelect:true,
                    method:'post',
                    rownumbers:'true',
                    showFooter:true,
                    title:'Kewajiban + Modal | Debit (-), Kredit (+)',
                ">
                <thead>
                    <tr>
                        <th data-options="field:'id',width:50,hidden:'true'">ID</th>
                        <th data-options="field:'pos',width:50,hidden:'true'">POS</th>
                        <th data-options="field:'code',width:100,align:'center'">Kode Akun</th>
                        <th data-options="field:'name',width:280">Nama Akun</th>
                        <th data-options="
                            field:'total',
                            width:120,
                            align:'right',
                            editor:{type:'numberbox',options:{precision:2}},
                            formatter:function(value,row){return calculateTotalCodeBalance(value)}">Jumlah (+/-)</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-accounting-code-balance-activa").datagrid("reload", "{{ url('finance/coa/data/grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&pos=D" + "&bookyear_id=" + {{ $bookyear->id }})
        $("#tb-accounting-code-balance-passiva").datagrid("reload", "{{ url('finance/coa/data/grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&pos=K" + "&bookyear_id=" + {{ $bookyear->id }})
        $("#tb-accounting-code-balance-activa").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 2,
            field: 'total'
        })
        $("#tb-accounting-code-balance-activa").datagrid({
            onEndEdit: function(index,row,changes) {
                if (changes.total == "") {
                    $("#tb-accounting-code-balance-activa").datagrid("updateRow",{
                        index: index,
                        row: { total: 0 }
                    })
                }
                var rowsData = $("#tb-accounting-code-balance-activa").datagrid("getData")
                var totalDebit = 0
                for (var i = 0; i < rowsData.rows.length; i++) {
                    totalDebit += parseFloat(rowsData.rows[i].total)
                }
                $("#tb-accounting-code-balance-activa").datagrid("reloadFooter",[{name: '<b>Total</b>',total: totalDebit}])
            }
        })
        $("#tb-accounting-code-balance-passiva").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 2,
            field: 'total'
        })
        $("#tb-accounting-code-balance-passiva").datagrid({
            onEndEdit: function(index,row,changes) {
                if (changes.total == "") {
                    $("#tb-accounting-code-balance-passiva").datagrid("updateRow",{
                        index: index,
                        row: { total: 0 }
                    })
                }
                var rowsData = $("#tb-accounting-code-balance-passiva").datagrid("getData")
                var totalCredit = 0
                for (var i = 0; i < rowsData.rows.length; i++) {
                    totalCredit += parseFloat(rowsData.rows[i].total)
                }
                $("#tb-accounting-code-balance-passiva").datagrid("reloadFooter",[{name: '<b>Total</b>',total: totalCredit}])
            }
        })
    })
    function calculateTotalCodeBalance(value) {
        let rupiahIDLocale = Intl.NumberFormat('id-ID')
        if (typeof(value) !== "undefined" && value !== "") {
            return rupiahIDLocale.format(value)
        } else {
            return '0'
        }
    }
</script>