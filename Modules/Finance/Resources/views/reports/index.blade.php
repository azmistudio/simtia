@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Laporan Keuangan</h5>
        </div>
        <div class="col-4 p-0 text-right">
            
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:275px">
        <div class="p-1">
            <ul class="easyui-tree">
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/transaction', 'body-accounting-report', 'Transaksi Keuangan')">Transaksi Keuangan</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/ledger', 'body-accounting-report', 'Buku Besar')">Buku Besar</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/profit-loss', 'body-accounting-report', 'Laba Rugi')">Laba Rugi</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/balance-sheet', 'body-accounting-report', 'Neraca')">Neraca</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/trial-balance', 'body-accounting-report', 'Neraca Percobaan')">Neraca Percobaan</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/equity-change', 'body-accounting-report', 'Perubahan Modal')">Perubahan Modal</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/cash-flow', 'body-accounting-report', 'Arus Kas')">Arus Kas</a></span></li>
                <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/audit', 'body-accounting-report', 'Audit Perubahan Data')">Audit Perubahan Data</a></span></li>
                <li>
                    <span>Transaksi Penerimaan</span>
                    <ul>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/class', 'body-accounting-report', 'Pembayaran Per Kelas')">Pembayaran Per Kelas</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/student', 'body-accounting-report', 'Pembayaran Per Santri')">Pembayaran Per Santri</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/student/arrear', 'body-accounting-report', 'Pembayaran Santri yang menunggak')">Pembayaran Santri yang menunggak</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/student/prospect/group', 'body-accounting-report', 'Pembayaran Per Kelompok Calon Santri')">Pembayaran Per Kelompok Calon Santri</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/student/prospect', 'body-accounting-report', 'Pembayaran Per Calon Santri')">Pembayaran Per Calon Santri</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/student/prospect/arrear', 'body-accounting-report', 'Pembayaran Calon Santri yang menunggak')">Pembayaran Calon Santri yang menunggak</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/recap', 'body-accounting-report', 'Rekapitulasi Penerimaan')">Rekapitulasi Penerimaan</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/recap/arrear', 'body-accounting-report', 'Rekapitulasi Tunggakan Santri')">Rekapitulasi Tunggakan Santri</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/other', 'body-accounting-report', 'Penerimaan Lain')">Penerimaan Lain</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/receipt/journal', 'body-accounting-report', 'Jurnal Penerimaan')">Jurnal Penerimaan</a></span></li>
                    </ul>
                </li>
                <li>
                    <span>Transaksi Pengeluaran</span>
                    <ul>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/expense/transaction', 'body-accounting-report', 'Transaksi Pengeluaran')">Transaksi Pengeluaran</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/expense/journal', 'body-accounting-report', 'Jurnal Pengeluaran')">Jurnal Pengeluaran</a></span></li>
                    </ul>
                </li>
                <li data-options="state:'closed'">
                    <span>Tabungan Santri</span>
                    <ul>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/saving/class', 'body-accounting-report', 'Tabungan per Kelas')">Tabungan per Kelas</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/saving/student', 'body-accounting-report', 'Tabungan per Santri')">Tabungan per Santri</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/saving/student/recap', 'body-accounting-report', 'Rekapitulasi Tabungan')">Rekapitulasi Tabungan</a></span></li>
                    </ul>
                </li>
                <li data-options="state:'closed'">
                    <span>Tabungan Pegawai</span>
                    <ul>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/saving/employee', 'body-accounting-report', 'Tabungan per Pegawai')">Tabungan per Pegawai</a></span></li>
                        <li><span><a href="javascript:void(0)" onclick="reportPage('finance/report/saving/employee/recap', 'body-accounting-report', 'Rekapitulasi Tabungan')">Rekapitulasi Tabungan</a></span></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="title">
            <h6><span id="title-accounting-report">-</span></h6>
        </div>
        <div id="body-accounting-report" class="p-1"><div id="loader-accounting-report" class="panel-loading" style="visibility:hidden;">Memuat ...</div></div>
    </div>
</div>
<script type="text/javascript">
    
    $(function () {
                
    })

</script>