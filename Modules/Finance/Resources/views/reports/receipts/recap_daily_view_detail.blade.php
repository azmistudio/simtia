@php
    $total = 0;
    $total_discount = 0;
    $total_grand = 0;
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
                        <td>Penerimaan</td>
                        <td>:</td>
                        <td><b>{{ $requests['type'] }}</b></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td><b>{{ $requests['trans_date'] }}</b></td>
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
                        <th class="text-center">Tanggal</th>
                        <th>Petugas</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Diskon</th>
                        <th width="40%">Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $x = 1; @endphp
                    @foreach ($details as $detail)
                    <tr>
                        <td class="text-center">{{ $x++ }}</td>
                        <td class="text-center">{{ $detail->trans_date }}</td>
                        <td>{{ $detail->employee }}</td>
                        <td class="text-right">Rp{{ number_format($detail->total,2) }}</td>
                        <td class="text-right">Rp{{ number_format($detail->discount_amount,2) }}</td>
                        <td>{{ $detail->transaction }}</td>
                    </tr>
                    @php 
                        $total += $detail->total;
                        $total_discount += $detail->discount_amount;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-center"><b>TOTAL</b></th>
                        <th class="text-right">Rp{{ number_format($total,2) }}</th>
                        <th class="text-right">Rp{{ number_format($total_discount,2) }}</th>
                        <th class="text-center">Rp{{ number_format(($total-$total_discount),2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>