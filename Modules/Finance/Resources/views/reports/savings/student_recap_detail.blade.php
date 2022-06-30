@php
    $total = 0;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-12 pl-2">
            <table style="width:100%;">
                <tbody>
                    <tr>
                        <td>Departemen</td>
                        <td>:</td>
                        <td><b>{{ $requests['department'] }}</b></td>
                        <td>Tabungan</td>
                        <td>:</td>
                        <td><b>{{ $requests['saving_type'] }}</b></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><b>{{ $requests['start_date'] }} s.d {{ $requests['end_date'] }}</b></td>
                        <td>Petugas</td>
                        <td>:</td>
                        <td><b>{{ $requests['employee'] }}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12 pl-2 mt-3">
            <table class="table table-bordered table-sm" style="width:100%;border-collapse:collapse">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th class="text-center" width="13%">Tanggal</th>
                        <th class="text-center" width="30%">Santri</th>
                        <th class="text-center">@if ($requests['type'] == 'credit') Setoran @else Tarikan @endif</th>
                        <th class="text-center">Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $x = 1; @endphp
                    @foreach ($details as $detail)
                    @php
                        $total += $requests['type'] == 'debit' ? $detail->debit : $detail->credit;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $x++ }}</td>
                        <td class="text-center">{{ $detail->trans_date }}</td>
                        <td class="">{{ $detail->student }}</td>
                        <td class="text-right">@if ($requests['type'] == 'debit') Rp{{ number_format($detail->debit,2) }} @else Rp{{ number_format($detail->credit,2) }} @endif</td>
                        <td class="">{{ $detail->employee }}</td>
                        <td class="">{{ $detail->remark }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-center"><b>TOTAL</b></th>
                        <th class="text-right">Rp{{ number_format($total,2) }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>