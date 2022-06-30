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
              <td style="width:3%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $payment_majors['department'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Tahun Buku</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors['book_year'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Kategori</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors['category'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Jenis Penerimaan</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payment_majors['receipt_type'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border" style="width:100%;" cellspacing="2" cellpadding="2">
            <tbody>
              <tr>
                <td width="40%" valign="top">
                  <fieldset style="height:200px;">
                    <legend><b>Pembayaran yang harus dilunasi</b></legend>
                    <table class="table no-border">
                      <tbody>
                        <tr>
                          <td>Pembayaran</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['receipt_type'] }}</td>
                        </tr>
                        <tr>
                          <td>Jumlah</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>Rp{{ number_format($payment_majors['amount'],2) }}</td>
                        </tr>
                        <tr>
                          <td>Pembayaran</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{!! $payment_majors['is_paid'] == 0 ? 'Belum Lunas' : '<b>Lunas</b>' !!}</td>
                        </tr>
                        <tr>
                          <td valign="top">Keterangan</td>
                          <td valign="top" style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['remark'] }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset>
                </td>
                <td valign="top">
                  <fieldset style="height:200px;">
                    <legend><b>Data {{ $requests->category_id == 1 ? 'Santri' : 'Calon Santri' }}</b></legend>
                    <table class="table no-border">
                      <tbody>
                        <tr>
                          <td style="width:15%;">{{ $requests->category_id == 1 ? 'NIS' : 'No. Daftar' }}</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['student_no'] }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">Nama</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['student_name'] }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">{{ $requests->category_id == 1 ? 'Kelas' : 'Kelompok' }}</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $requests->class }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">HP</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['mobile'] }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">Telpon</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['phone'] }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;" valign="top">Alamat</td>
                          <td valign="top" style="width: 1%;text-align:center;">:</td>
                          <td>{{ $payment_majors['address'] }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset>
                </td>
              </tr>
            </tbody>
        </table>
        <br/>
        <fieldset style="padding:15px;">
          <legend style="font-size:13px;"><b>Pembayaran Cicilan</b></legend>
          <table style="width:100%;">
            <thead>
              <tr>
                <td class="text-center">No.</td>
                <td class="text-center">No. Jurnal/Tgl.</td>
                <td class="text-center">Rek. Kas</td>
                <td class="text-center">Besar</td>
                <td class="text-center">Diskon</td>
                <td class="text-center">Keterangan</td>
                <td class="text-center">Petugas</td>
              </tr>
            </thead>
            <tbody>
              @php $x = 1; @endphp
              @foreach ($instalments as $instalment) 
                <tr>
                  <td class="text-center">{{ $x }}</td>
                  <td class="text-center"><b>{{ $instalment->cash_no  }} </b><br/> {{ $instalment->journal_date }}</td>
                  <td>{{ $instalment->code . ' ' . $instalment->name }}</td>
                  <td class="text-right">Rp{{ number_format($instalment->total, 2) }}</td>
                  <td class="text-right">Rp{{ number_format($instalment->discount_amount, 2) }}</td>
                  <td>{{ $instalment->remark }}</td>
                  <td>{{ $instalment->logged }}</td>
                </tr>
              @php $x++; @endphp
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" class="text-center">Total: </td>
                <td class="text-right"><b>Rp{{ number_format($total->total, 2) }}</b></td>
                <td class="text-right"><b>Rp{{ number_format($total->discount, 2) }}</b></td>
                <td>Sisa: <b>Rp{{ number_format($payment_majors['amount'] - $total->total, 2) }}</b></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </fieldset>
      </div>
  </body>
</html>