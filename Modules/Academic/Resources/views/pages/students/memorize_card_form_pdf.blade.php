@php
    $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - FORMULIR KARTU SETORAN HAFALAN SANTRI</title>
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
      <div class="text-center" style="font-size:16px;"><b>FORMULIR KARTU SETORAN HAFALAN SANTRI</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
            <tbody>
                <tr>
                    <td style="width:3%;">Departemen</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td style="width:30%;">________________________</td>
                    <td style="width:3%;">Kelas</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td style="width:30%;">____________________________</td>
                    <td rowspan="3" valign="top" style="border:solid 1px #333;text-align: center;"><u>Paraf Guru</u></td>
                </tr>
                <tr>
                    <td style="width:3%;">Tahun Ajaran</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td>________________________</td>
                    <td style="width:3%;">Guru</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td style="width:30%;">____________________________</td>
                </tr>
                <tr>
                    <td style="width:3%;">Tingkat/Semester</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td>________ / _____________</td>
                    <td style="width:3%;">Hari/Tanggal</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td style="width:30%;">_______________/____________</td>
                </tr>
                <tr>
                    <td style="width:3%;">Keterangan</td>
                    <td style="width: 1%;text-align:center;">:</td>
                    <td colspan="5"></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <br/>
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2" width="5%">No.</th>
                    <th class="text-center" rowspan="2" width="10%">NIS</th>
                    <th rowspan="2">Nama</th>
                    <th class="text-center" colspan="2">Dari</th>
                    <th class="text-center" colspan="2">Sampai</th>
                    <th class="text-center" rowspan="2">Status</th>
                </tr>
                <tr>
                    <th class="text-center">Surat</th>
                    <th class="text-center">Ayat</th>
                    <th class="text-center">Surat</th>
                    <th class="text-center">Ayat</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i < 41; $i++)
                <tr>
                  <td class="text-center">{{ $i }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                @endfor
            </tbody>
        </table>
      </div>
  </body>
</html>