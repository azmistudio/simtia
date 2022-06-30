@inject('reference', 'Modules\Finance\Http\Controllers\FinanceController')
@inject('paymentMajorEloquent', 'Modules\Finance\Repositories\Receipt\PaymentMajorEloquent')
@php
    $arr_total = array();
    $instalment = 0;
    $amount = 0;
    $payment = 0;
    $discount = 0;
    $balance = 0;
    for ($x=0; $x < count($receipt_types); $x++) 
    { 
        for($y = 0; $y < 8; $y++)
        {
            $arr_total[$x * 8 + $y] = 0;
        }
    }
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2">
            <h6>Rekapitulasi Tunggakan Santri</h6>
        </div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportRecapArrear('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="container-fluid" style="min-width:1800px;">
    <div class="row">
        <div class="col-12 p-2" >
            <table border="1" style="width:100%;border-collapse:collapse">
                <thead>
                    <tr height="25" bgcolor="#CCCFFF">
                        <th rowspan="2" class="text-center" width="4%">No.</th>
                        <th rowspan="2" class="text-center" width="5%">NIS</th>
                        <th rowspan="2" class="text-center">Nama</th>
                        <th rowspan="2" class="text-center" width="5%">Tingkat</th>
                        <th rowspan="2" class="text-center" width="7%">Kelas</th>
                        @foreach ($receipt_types as $type)
                        <th colspan="8" class="text-center">{{ $type->name }}</th>
                        @endforeach
                    </tr>
                    <tr height="25" bgcolor="#CCCFFF">
                        @foreach ($receipt_types as $type)
                        <th class="text-center">Cicilan</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Pembayaran</th>
                        <th class="text-center">Diskon</th>
                        <th class="text-center">Sisa</th>
                        <th class="text-center">Tgl.Akhir</th>
                        <th class="text-center">Byr.Akhir</th>
                        <th class="text-center">Ket.Akhir</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $x = 1; @endphp
                    {{-- students --}}
      				@foreach ($students as $student)
                    <tr height="25">
                        <td class="text-center">{{ $x++ }}</td>
                        <td class="text-center">{{ $student->student_no }}</td>
                        <td class="text-left">
                            &nbsp;<b>{{ ucwords($student->name) }}</b><br/>
                            <ul style="padding-left:25px;padding-top: 5px;">
                                <li>Ayah/Ibu: {{ $student->father }} / {{ $student->mother }}</li>
                                <li>Alamat: {{ $student->address }}, Kode Pos {{ $student->postal_code }}</li>
                                <li>Telpon: {{ $student->phone }}</li>
                                <li>HP Orang Tua: {{ $student->father_mobile }} / {{ $student->mother_mobile }}</li>
                            </ul>
                        </td>
                        <td class="text-center">{{ $student->grade }}</td>
                        <td class="text-center">{{ strtoupper($student->class) }}</td>
                        {{-- loop by receipt type --}}
                        @foreach ($receipt_types as $type)
                            @php
                                $i = 0; 
                                $recapStudentArrear = $paymentMajorEloquent->recapStudentArrear($bookyear->id, $type->id, $student->student_id);
                            @endphp
                            @if (count($recapStudentArrear) > 0)
                                {{-- payment info --}}
                                @foreach ($recapStudentArrear as $data)
                                <td class="text-right">Rp{{ number_format($data->instalment,2) }}</td>
                                <td class="text-right">Rp{{ number_format($data->amount,2) }}</td>
                                <td class="text-right">Rp{{ number_format($data->total + $data->total_discount,2) }}</td>
                                <td class="text-right">Rp{{ number_format($data->total_discount,2) }}</td>
                                <td class="text-right">Rp{{ number_format($data->amount - ($data->total + $data->total_discount),2) }}</td>
                                {{-- last payment --}}
                                @php
                                    $instalment += $data->instalment;
                                    $amount += $data->amount;
                                    $payment += $data->total + $data->total_discount;
                                    $discount += $data->total_discount;
                                    $balance += $amount - $payment;
                                    //
                                    $arr_total[$i] = $instalment; 
                                    $arr_total[$i + 1] = $amount; 
                                    $arr_total[$i + 2] = $payment; 
                                    $arr_total[$i + 3] = $discount; 
                                    $arr_total[$i + 4] = $balance; 
                                    $recapStudentArrearLast = $paymentMajorEloquent->recapStudentArrearLast($bookyear->id, $type->id, $student->student_id);
                                    $i++;
                                @endphp
                                <td class="text-center">{{ $reference->formatDate($recapStudentArrearLast->trans_date,'local') }}</td>
                                <td class="text-right">Rp{{ number_format($recapStudentArrearLast->total,2) }}</td>
                                <td class="text-center">{{ $recapStudentArrearLast->remark }}</td>
                                @endforeach
                            @else
                                <td class="text-right">Rp{{ number_format(0,2) }}</td>
                                <td class="text-right">Rp{{ number_format(0,2) }}</td>
                                <td class="text-right">Rp{{ number_format(0,2) }}</td>
                                <td class="text-right">Rp{{ number_format(0,2) }}</td>
                                <td class="text-right">Rp{{ number_format(0,2) }}</td>
                                <td class="text-center">-</td>
                                <td class="text-right">Rp{{ number_format(0,2) }}</td>
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                    <tr height="25" bgcolor="#CCEFFF">
                        <td colspan="5" class="text-center"><b>TOTAL</b></td>
                        @for ($i=0; $i < count($receipt_types); $i++) 
                            @for ($j = 0; $j < 8; $j++)
                                @if ($j < 5)
                                <td class="text-right">
                                    <b>Rp{{ number_format($arr_total[$i + $j],2) }}</b>
                                </td>
                                @else
                                <td class="text-center"></td>
                                @endif
                            @endfor
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function exportReportRecapArrear(document) {
        var payload = {
            department_id: {{ $requests['department_id'] }},
            department: "{{ $requests['department'] }}",
            class_id: {{ $requests['class_id'] }},
            class: "{{ $requests['class'] }}",
            grade_id: {{ $requests['grade_id'] }},
            grade: "{{ $requests['grade'] }}",
            schoolyear_id: {{ $requests['schoolyear_id'] }},
            schoolyear: "{{ $requests['schoolyear'] }}",
        }
        exportDocument("{{ url('finance/report/receipt/recap/arrear/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
</script>