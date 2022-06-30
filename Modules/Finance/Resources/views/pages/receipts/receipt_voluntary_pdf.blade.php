@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - DATA PENERIMAAN PEMBAYARAN SUKARELA</title>
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
      <div class="text-center" style="font-size:16px;"><b>DATA PENERIMAAN PEMBAYARAN SUKARELA</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:3%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $categories->department }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Tahun Buku</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $bookyear->book_year }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Kategori</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $categories->category }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Jenis Penerimaan</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $categories->name }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border" style="width:100%;" cellspacing="2" cellpadding="2">
            <tbody>
              <tr>
                <td valign="top">
                  <fieldset>
                    <legend><b>Data Santri</b></legend>
                    <table class="table no-border">
                      <tbody>
                        <tr>
                          <td style="width:10%;">{{ $requests->category_id == 2 ? 'NIS' : 'No. Daftar' }}</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $requests->student_no }}</td>
                        </tr>
                        <tr>
                          <td style="width:10%;">Nama</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $requests->student_name }}</td>
                        </tr>
                        <tr>
                          <td style="width:10%;">{{ $requests->category_id == 2 ? 'Kelas' : 'Kelompok' }}</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $requests->class }}</td>
                        </tr>
                        <tr>
                          <td style="width:10%;">HP</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $students['mobile'] }}</td>
                        </tr>
                        <tr>
                          <td style="width:10%;">Telpon</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $students['phone'] }}</td>
                        </tr>
                        <tr>
                          <td style="width:10%;" valign="top">Alamat</td>
                          <td valign="top" style="width: 1%;text-align:center;">:</td>
                          <td>{{ $students['address'] }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset>
                </td>
              </tr>
            </tbody>
        </table>
        <br/>
        <table style="width:100%;">
          <thead>
            <tr>
              <td class="text-center">No.</td>
              <td class="text-center">No. Jurnal/Tgl.</td>
              <td class="text-center">Rek. Kas</td>
              <td class="text-center">Jumlah</td>
              <td class="text-center">Keterangan</td>
              <td class="text-center">Petugas</td>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($payments as $payment) 
              <tr>
                <td class="text-center">{{ $x }}</td>
                <td class="text-center"><b>{{ $payment->cash_no  }} </b><br/> {{ $payment->journal_date }}</td>
                <td>{{ $payment->code . ' ' . $payment->name }}</td>
                <td class="text-right">Rp{{ number_format($payment->total, 2) }}</td>
                <td>{{ $payment->remark }}</td>
                <td>{{ $payment->logged }}</td>
              </tr>
            @php $x++; @endphp
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-center">Total: </td>
              <td class="text-right"><b>Rp{{ number_format($total->total, 2) }}</b></td>
              <td></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
  </body>
</html>