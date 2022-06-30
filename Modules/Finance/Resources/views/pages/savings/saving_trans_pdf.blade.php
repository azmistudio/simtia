@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - TABUNGAN</title>
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
      <div class="text-center" style="font-size:16px;"><b>TABUNGAN {{ $data->is_employee == 0 ? 'SANTRI' : 'PEGAWAI' }}</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:3%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $data->department }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Tahun Buku</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $bookyear['book_year'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Jenis Tabungan</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $saving_type['name'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border" style="width:100%;" cellspacing="2" cellpadding="2">
            <tbody>
              <tr>
                <td valign="top">
                  <fieldset style="height:180px;">
                    <legend><b>Data {{ $data->is_employee == 0 ? 'Santri' : 'Pegawai' }}</b></legend>
                    <table class="table no-border">
                      <tbody>
                        <tr>
                          <td style="width:15%;">{{ $data->is_employee == 0 ? 'NIS' : 'NIP' }}</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $data->person_no }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">Nama</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $data->person_name }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">{{ $data->is_employee == 0 ? 'Kelas' : 'Bagian' }}</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $data->person_info  }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">HP</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ !empty($person['mobile']) ? $person['mobile'] : '' }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;">Telpon</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ !empty($person['phone']) ? $person['phone'] : '' }}</td>
                        </tr>
                        <tr>
                          <td style="width:15%;" valign="top">Alamat</td>
                          <td valign="top" style="width: 1%;text-align:center;">:</td>
                          <td>{{ !empty($person['address']) ? $person['address'] : '' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </fieldset>
                </td>
                <td width="45%" valign="top">
                  <fieldset style="height:180px;">
                    <legend><b>Informasi Tabungan</b></legend>
                    <table class="table no-border">
                      <tbody>
                        <tr>
                          <td>Saldo</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $summary['balance'] }}</td>
                        </tr>
                        <tr>
                          <td>Jumlah Setoran</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $summary['total_deposit'] }}</td>
                        </tr>
                        <tr>
                          <td>Setoran Terakhir</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $summary['last_deposit'] }} {{ $summary['last_deposit_date'] }}</td>
                        </tr>
                        <tr>
                          <td>Jumlah Penarikan</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $summary['total_withdraw'] }}</td>
                        </tr>
                        <tr>
                          <td>Penarikan Terakhir</td>
                          <td style="width: 1%;text-align:center;">:</td>
                          <td>{{ $summary['last_withdraw'] }} {{ $summary['last_withdraw_date'] }}</td>
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
          <legend style="font-size:13px;"><b>Transaksi Tabungan</b></legend>
          <table style="width:100%;">
            <thead>
              <tr>
                <td class="text-center">No.</td>
                <td class="text-center">No. Jurnal/Tgl.</td>
                <td class="text-center">Debit</td>
                <td class="text-center">Kredit</td>
                <td class="text-center">Keterangan</td>
                <td class="text-center">Petugas</td>
              </tr>
            </thead>
            <tbody>
              @php $x = 1; @endphp
              @foreach ($data->rows as $trans) 
                <tr>
                  <td class="text-center">{{ $x }}</td>
                  <td class="text-center">{!! $trans->journal !!}</td>
                  <td class="text-right">Rp{{ $trans->debit }}</td>
                  <td class="text-right">Rp{{ $trans->credit }}</td>
                  <td>{{ $trans->remark }}</td>
                  <td>{{ $trans->logged }}</td>
                </tr>
              @php $x++; @endphp
              @endforeach
            </tbody>
          </table>
        </fieldset>
      </div>
  </body>
</html>