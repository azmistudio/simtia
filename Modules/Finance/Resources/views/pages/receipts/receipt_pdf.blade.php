@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - KUITANSI PEMBAYARAN</title>
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
      <br/>
      <br/>
      <div class="text-center" style="font-size:16px;"><b>TANDA BUKTI PEMBAYARAN</b></div>
      <br/>
      <br/>
      <table class="table no-border" width="100%">
        <tbody>
          <tr>
            <td width="65%">
              <p>Telah terima dari:</p>
              <table class="table no-border" style="font-size: 14px;font-weight:700;margin-left:20px;">
                <tbody>
                  @if ($requests['category_id'] != 5)
                  <tr>
                    <td style="width:3%;">{{ $requests['category_id'] == 1 ? 'NIS' : 'No. Daftar' }}</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td style="width:30%;"><b>{{ $requests['student_no'] }}</b></td>
                  </tr>
                  @endif
                  <tr>
                    <td style="width:3%;">Nama</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td><b>{{ $requests['student_name'] }}</b></td>
                  </tr>
                  @if ($requests['category_id'] != 5)
                  <tr>
                    <td style="width:3%;">{{ $requests['category_id'] == 1 ? 'Kelas' : 'Kelompok' }}</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td><b>{{ $requests['class'] }}</b></td>
                  </tr>
                  @endif
                  <tr>
                    <td style="width:3%;">Tanggal</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td><b>{{ $receipt_majors['trans_date'] }}</b></td>
                  </tr>
                </tbody>
              </table>
            </td>
            <td valign="top" style="text-align:right;"><b>No. {{ $receipt_majors['cash_no'] }}</b></td>
          </tr>
        </tbody>
      </table>
      <div>
        <p>uang sejumlah <b><i>{{ $values['total'] }}</i> ({{ $values['counted'] }} rupiah)</b> untuk {{ $receipt_majors['transaction'] }}</p>
        <br/>
        <table class="table no-border" width="100%">
          <tbody>
            <tr>
              <td width="80%">
                <table class="table" width="100%">
                  <tbody>
                    <tr>
                      <td>
                        <b>Keterangan:</b>
                        <ul>
                          @if ($requests['category_id'] == 1 || $requests['category_id'] == 3)
                          <li>Sisa pembayaran: <b>{{ $values['balance'] }}</b></li>
                          @endif
                          <li>Tanggal cetak: <b>{{ date('d/m/Y H:i:s') }}</b></li>
                          <li>Petugas: <b>{{ $receipt_majors['name'] }}</b></li>
                        </ul>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td width="20%">
                <table class="table no-border" width="100%">
                  <tbody>
                    <tr>
                      <td class="text-center">
                        Yang menerima
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        ( {{ $receipt_majors['name'] }} )
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <br/>
      <br/>
      <br/>
      <br/>
      <hr style="border-top: 1px dashed;" />
      <br/>
      <br/>
      <br/>
      <br/>
      <div>
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
      <br/>
      <br/>
      <br/>
      <div class="text-center" style="font-size:16px;"><b>TANDA BUKTI PEMBAYARAN</b></div>
      <br/>
      <br/>
      <br/>
      <table class="table no-border" width="100%">
        <tbody>
          <tr>
            <td width="65%">
              <p>Telah terima dari:</p>
              <table class="table no-border" style="font-size: 14px;font-weight:700;margin-left:20px;">
                <tbody>
                  @if ($requests['category_id'] != 5)
                  <tr>
                    <td style="width:3%;">{{ $requests['category_id'] == 1 ? 'NIS' : 'No. Daftar' }}</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td style="width:30%;"><b>{{ $requests['student_no'] }}</b></td>
                  </tr>
                  @endif
                  <tr>
                    <td style="width:3%;">Nama</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td><b>{{ $requests['student_name'] }}</b></td>
                  </tr>
                  @if ($requests['category_id'] != 5)
                  <tr>
                    <td style="width:3%;">{{ $requests['category_id'] == 1 ? 'Kelas' : 'Kelompok' }}</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td><b>{{ $requests['class'] }}</b></td>
                  </tr>
                  @endif
                  <tr>
                    <td style="width:3%;">Tanggal</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td><b>{{ $receipt_majors['trans_date'] }}</b></td>
                  </tr>
                </tbody>
              </table>
            </td>
            <td valign="top" style="text-align:right;"><b>No. {{ $receipt_majors['cash_no'] }}</b></td>
          </tr>
        </tbody>
      </table>
      <div>
        <p>uang sejumlah <b><i>{{ $values['total'] }}</i> ({{ $values['counted'] }} rupiah)</b> untuk {{ $receipt_majors['transaction'] }}</p>
        <br/>
        <table class="table no-border" width="100%">
          <tbody>
            <tr>
              <td width="80%">
                <table class="table" width="100%">
                  <tbody>
                    <tr>
                      <td>
                        <b>Keterangan:</b>
                        <ul>
                          @if ($requests['category_id'] == 1 || $requests['category_id'] == 3)
                          <li>Sisa pembayaran: <b>{{ $values['balance'] }}</b></li>
                          @endif
                          <li>Tanggal cetak: <b>{{ date('d/m/Y H:i:s') }}</b></li>
                          <li>Petugas: <b>{{ $receipt_majors['name'] }}</b></li>
                        </ul>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td width="20%">
                <table class="table no-border" width="100%">
                  <tbody>
                    <tr>
                      <td class="text-center">
                        Yang menyerahkan
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  </body>
</html>