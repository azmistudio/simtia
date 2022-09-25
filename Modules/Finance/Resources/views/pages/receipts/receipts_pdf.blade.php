@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - DATA PENERIMAAN PEMBAYARAN</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
        <table class="table no-border" style="width:100%;">
            <tbody>
                <tr>
                    <th rowspan="2" width="100px"><img src="file:///{{ $logo }}" height="80px" /></th>
                    <td><b>{{ strtoupper($profile['name']) }}</b></td>
                </tr>
                <tr>
                    <td style="font-size:11px;">
                        {{ $profile['address'] }}<br/>
                        Telpon: {{ $profile['phone'] }} - Faksimili: {{ $profile['fax'] }}<br/>
                        Website: {{ $profile['web'] }} - Email: {{ $profile['email'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr/>
    <div id="body">
      <br/>
      <div class="text-center" style="font-size:16px;"><b>DATA PENERIMAAN PEMBAYARAN</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors[0]['department'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Tahun Buku</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors[0]['book_year'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Kategori</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors[0]['category'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Jenis Penerimaan</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors[0]['receipt_type'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">{{ $payment_majors[0]['category_id'] == 1 ? 'NIS' : 'No. Daftar' }}</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:34%;">{{ $payment_majors[0]['category_id'] == 1 ? $payment_majors[0]['student']->student_no : $payment_majors[0]['student']->registration_no }}</td>
              <td style="width:15%;">HP</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:34%;">{{ $payment_majors[0]['student']->mobile }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Nama</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ ucwords($payment_majors[0]['student']->name) }}</td>
              <td style="width:15%;">Telpon</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:38%;">{{ $payment_majors[0]['student']->phone }}</td>
            </tr>
            <tr>
              <td style="width:15%;">{{ $payment_majors[0]['category_id'] == 1 ? 'Kelas' : 'Kelompok' }}</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests->class }}</td>
            </tr>
          </tbody>
        </table>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;" valign="top">Alamat</td>
              <td valign="top" style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors[0]['student']->address }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table style="width:100%;">
          <thead>
            <tr>
              <th width="5%">No.</th>
              <th>Periode Bayar</th>
              <th>Jumlah Bayar</th>
              <th>Cicilan</th>
              <th>Status</th>
              <th>Keterangan</th>
            </tr>
          </thead>
          <tbody>
            @php $no = 1; @endphp
            @foreach ($payment_majors as $payment_major)
            @php
              $instalments = $receiptMajorEloquent->dataInstalment($payment_major->id);
              $instalment_totals = $receiptMajorEloquent->totalInstalments($payment_major->id);
              $no_sub = 1;
            @endphp
            <tr>
              <td class="text-center">{{ $no++ }}</td>
              <td class="text-center">{{ $payment_major->period_payment }}</td>
              <td class="text-right">Rp{{ number_format($payment_major->amount,2) }}</td>
              <td class="text-right">Rp{{ number_format($payment_major->instalment,2) }}</td>
              <td class="text-center">{{ $payment_major->is_paid == 1 ? 'Lunas' : 'Belum Lunas' }}</td>
              <td>{{ $payment_major->remark }}</td>
            </tr>
            <tr>
              <td colspan="6">
                <table class="table no-border striped" style="width:100%;">
                  <thead>
                    <tr>
                      <th class="text-center">No.</th>
                      <th class="text-center">No. Jurnal/Tgl.</th>
                      <th class="text-left">Rek. Kas</th>
                      <th class="text-right">Besar</th>
                      <th class="text-right">Diskon</th>
                      <th class="text-left">Keterangan</th>
                      <th class="text-left">Petugas</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($instalments as $instalment)
                    @if ($instalment->major_id == $payment_major->id)
                    <tr>
                      <td class="text-center">{{ $no_sub++ }}</td>
                      <td class="text-center"><b>{{ $instalment->cash_no  }} </b><br/> {{ $instalment->journal_date }}</td>
                      <td>{{ $instalment->code . ' ' . $instalment->name }}</td>
                      <td class="text-right">Rp{{ number_format($instalment->total, 2) }}</td>
                      <td class="text-right">Rp{{ number_format($instalment->discount_amount, 2) }}</td>
                      <td>{{ $instalment->remark }}</td>
                      <td>{{ $instalment->logged }}</td>
                    </tr>
                    @endif
                    @endforeach
                  </tbody>
                  <tfoot>
                    @foreach ($instalment_totals as $instalment_total)
                    @if ($instalment_total->major_id == $payment_major->id)
                    <tr style="border-top:solid 1px #333;">
                      <td colspan="3" class="text-center">Total: </td>
                      <td class="text-right"><b>Rp{{ number_format($instalment_total->total, 2) }}</b></td>
                      <td class="text-right"><b>Rp{{ number_format($instalment_total->discount, 2) }}</b></td>
                      <td>Sisa: <b>Rp{{ number_format($payment_major->amount - $instalment_total->total, 2) }}</b></td>
                      <td></td>
                    </tr>
                    @endif
                    @endforeach
                  </tfoot>
                </table>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </body>
</html>