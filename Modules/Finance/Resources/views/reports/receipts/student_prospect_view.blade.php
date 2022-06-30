@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@php
    $totalPaymentMajor = 0;
    $totalPaymentReceipt = 0;
    $totalPaymentDiscount = 0;
    $totalPaymentRemain = 0;
    $totalPaymentVoluntary = 0;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2">
            <h6>Data Pembayaran: <br/><b>{{ $requests['registration_no'] }} - {{ $requests['student'] }}</b></h6>
        </div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReportPaymentProspect('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportPaymentProspect('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-2">
            <table border="1" style="width:100%;border-collapse:collapse">
                <tbody>
                    {{-- mandatory --}}
                    @foreach ($mandatories as $mandatory)
                    @php 
                        $receiptMajorTotal = $receiptMajorEloquent->totalPaymentReceipt($mandatory->id); 
                        $receiptMajorLast = $receiptMajorEloquent->lastPaymentReceipt($mandatory->id);
                        $remain = $mandatory->amount - ($receiptMajorTotal->total_receipt + $receiptMajorTotal->total_discount);
                        $totalPaymentMajor += $mandatory->amount;
                        $totalPaymentReceipt += ($receiptMajorTotal->total_receipt + $receiptMajorTotal->total_discount);
                        $totalPaymentDiscount += $receiptMajorTotal->total_discount;
                        $totalPaymentRemain += $remain;
                    @endphp
                    <tr height="35">
                        <td colspan="4" bgcolor="#CCCFFF"><b>{{ $mandatory->getReceipt->name }}</b></td>
                    </tr>
                    <tr height="25">
                        <td width="20%" bgcolor="#CCFFFF"><strong>Total Bayaran</strong></td>
                        <td width="15%" bgcolor="#FFFFFF" align="right">Rp{{ number_format($mandatory->amount,2) }}</td>
                        <td width="22%" bgcolor="#CCFFFF" align="center"><strong>Pembayaran Terakhir</strong></td>
                        <td width="43%" bgcolor="#CCFFFF" align="center"><strong>Keterangan</strong></td>
                    </tr>
                    <tr height="25">
                        <td bgcolor="#CCFFFF"><strong>Jumlah Besar Pembayaran</strong></td>
                        <td bgcolor="#FFFFFF" align="right">Rp{{ number_format($receiptMajorTotal->total_receipt + $receiptMajorTotal->total_discount, 2) }}</td>
                        <td bgcolor="#FFFFFF" align="center" valign="top" rowspan="3">
                            Rp{{ number_format($receiptMajorLast[0]->total,2) }}<br/>
                            Tanggal: {{ $receiptMajorLast[0]->trans_date }}<br/>
                            Diskon: Rp{{ $receiptMajorLast[0]->discount_amount }}<br/>
                            No. Jurnal: {{ $receiptMajorLast[0]->getJournal->cash_no }}
                        </td>
                        <td bgcolor="#FFFFFF" align="left" valign="top" rowspan="3">{{ $mandatory->remark }}</td>
                    </tr>
                    <tr height="25">
                        <td bgcolor="#CCFFFF"><strong>Jumlah Diskon</strong> </td>
                        <td bgcolor="#FFFFFF" align="right">Rp{{ number_format($receiptMajorTotal->total_discount,2) }}</td>
                    </tr>
                    <tr height="25">
                        <td bgcolor="#CCFFFF"><strong>Sisa Bayaran</strong> </td>
                        <td bgcolor="#FFFFFF" align="right">Rp{{ number_format($remain,2) }}</td>
                    </tr>
                    <tr height="3">
                        <td colspan="4" bgcolor="#E8E8E8">&nbsp;</td>
                    </tr>
                    @endforeach
                    {{-- voluntary --}}
                    @foreach ($voluntaries as $voluntary)
                    @php 
                        $receiptVoluntaryTotal = $receiptVoluntaryEloquent->totalPaymentReceipt($voluntary->receipt_id,$voluntary->student_id,1); 
                        $receiptVoluntaryLast = $receiptVoluntaryEloquent->lastPaymentReceipt($voluntary->receipt_id,$voluntary->student_id,1);
                        $totalPaymentVoluntary += $receiptVoluntaryTotal->total_receipt;
                    @endphp
                    <tr height="35">
                        <td colspan="4" bgcolor="#CCCFFF"><b>{{ $voluntary->getReceipt->name }}</b></td>
                    </tr>
                    <tr height="25">
                        <td width="22%" bgcolor="#CCFFFF" align="center"><strong>Total Pembayaran</strong> </td>
                        <td width="22%" bgcolor="#CCFFFF" align="center"><strong>Pembayaran Terakhir</strong></td>
                        <td width="50%" colspan="2" bgcolor="#CCFFFF" align="center"><strong>Keterangan</strong></td>
                    </tr>
                    <tr height="25">
                        <td bgcolor="#FFFFFF" align="center">Rp{{ number_format($receiptVoluntaryTotal->total_receipt) }}</td>
                        <td bgcolor="#FFFFFF" align="center">
                            Rp{{ isset($receiptVoluntaryLast[0]) ? number_format($receiptVoluntaryLast[0]->total,2) : 0 }}<br/>
                            Tanggal: {{ isset($receiptVoluntaryLast[0]) ? $receiptVoluntaryLast[0]->trans_date : '-' }}<br/>
                        </td>
                        <td colspan="2" bgcolor="#FFFFFF" align="left">&nbsp;</td>
                    </tr>
                    <tr height="3">
                        <td colspan="4" bgcolor="#E8E8E8">&nbsp;</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br/>
            <h6>REKAPITULASI PEMBAYARAN</h6>
        </div>
        <div class="col-6 p-2">
            <table border="1" style="width:100%;border-collapse:collapse" cellpadding="5">
                <tbody>
                    <tr>
                        <td colspan="2" bgcolor="#87C7F4" style="font-size:14px;"><b>Iuran Wajib Santri</b></td>
                    </tr>
                    <tr>
                        <td bgcolor="#E6F5FF">Total Semua Besar Bayaran</td>
                        <td class="text-right">Rp{{ number_format($totalPaymentMajor,2) }}</td>
                    </tr>
                    <tr>
                        <td bgcolor="#E6F5FF">Total Semua Pembayaran</td>
                        <td class="text-right">Rp{{ number_format($totalPaymentReceipt,2) }}</td>
                    </tr>
                    <tr>
                        <td bgcolor="#E6F5FF">Total Semua Diskon</td>
                        <td class="text-right">Rp{{ number_format($totalPaymentDiscount,2) }}</td>
                    </tr>
                    <tr>
                        <td bgcolor="#E6F5FF">Total Semua Sisa Tagihan</td>
                        <td class="text-right">Rp{{ number_format($totalPaymentRemain,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6 p-2">
            <table border="1" style="width:100%;border-collapse:collapse" cellpadding="5">
                <tbody>
                    <tr>
                        <td colspan="2" bgcolor="#87C7F4" style="font-size:14px;"><b>Iuran Sukarela Santri</b></td>
                    </tr>
                    <tr>
                        <td bgcolor="#E6F5FF">Total Semua Pembayaran</td>
                        <td class="text-right">Rp{{ number_format($totalPaymentVoluntary,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function exportReportPaymentProspect(document) {
       var payload = {
            is_prospect: 1,
            student_id: {{ $requests['student_id'] }},
            student: "{{ $requests['student'] }}",
            department: "{{ $requests['department'] }}",
            bookyear_id: {{ $requests['bookyear_id'] }}, 
            start_date: "{{ $requests['start_date'] }}",
            end_date: "{{ $requests['end_date'] }}",
            prospect_group: "{{ $requests['prospect_group'] }}",
            admission: "{{ $requests['admission'] }}",
            registration_no: "{{ $requests['registration_no'] }}",
        }
        exportDocument("{{ url('finance/report/receipt/student/prospect/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
</script>