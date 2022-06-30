@inject('reference', 'Modules\HR\Http\Controllers\HRController')
@php
  $photo = !empty($rows->photo) ? storage_path('app/public/uploads/employee/'.$rows->photo) : public_path('img/default-user.png');
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ strtoupper($profile['name']) }} - Data Personal SDM</title>
    <style type="text/css">
      body { margin: 0; padding: 0; font-size: 11px; font-family: "Segoe UI", "Open Sans", serif !important; }
      #header { top: 0; margin-bottom: 10px; background-color: #fff; }
      .text-left { text-align: left; }
      .text-center { text-align: center; }
      .text-right { text-align: right; }
      .break { page-break-before: avoid; }
      .must-break { page-break-before: always; }
      .center { margin: 0 auto; position: relative; display: flex; justify-content: center; }
    </style>
  </head>
  <body>
    <div id="header">
      <table>
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
      <div class="text-center" style="font-size:16px;"><b>Data Personal SDM</b></div>
      <br/>
      <br/>
      <br/>
      <div class="">
        <table>
          <tbody style="font-size:13px;">
            <tr>
              <th rowspan="6" style="padding-right:30px;"><img src="file:///{{ $photo }}" height="125px" /></th>
              <td><b>NIP</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->employee_id }}</td>
            </tr>
            <tr>
              <td><b>Nama</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->title_first .' '. $rows->name .' '. $rows->title_end }}</td>
            </tr>
            <tr>
              <td><b>Jenis Kelamin</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $reference->getGender()[$rows->gender] }}</td>
            </tr>
            <tr>
              <td><b>Tempat, Tanggal Lahir</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->pob . ', ' . $rows->dob->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td><b>Agama</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>Islam</td>
            </tr>
            <tr>
              <td><b>Status Aktif</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $reference->getActive()[$rows->is_active] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <br/>
      <br/>
      <hr/>
      <div>
        <table style="width:100%;">
          <thead>
            <tr>
              <th colspan="3"><br/>Informasi Kontak<br/><br/><br/></th>
              <th colspan="3"><br/>Informasi Lain<br/><br/><br/></th>
            </tr>
          </thead>
          <tbody style="font-size:13px;">
            <tr>
              <td style="width:125px;"><b>No. Identitas</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->national_id }}</td>
              <td style="width:125px;padding-left:10px;"><b>Bagian</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->getSection->name }}</td>
            </tr>
            <tr>
              <td><b>Email</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->email }}</td>
              <td style="width:125px;padding-left:10px;"><b>Tanggal Bekerja</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->work_start->format('d/m/Y') }}</td>
            </tr>
            <tr>
              <td><b>No. Telpon</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->phone }}</td>
              <td style="padding-left:10px;"><b>Status Nikah</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $reference->getMarital()[$rows->marital] }}</td>
            </tr>
            <tr>
              <td><b>No. Handphone</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->mobile }}</td>
              <td style="padding-left:10px;"><b>Suku</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->getTribe->name }}</td>
            </tr>
            <tr>
              <td><b>Alamat</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td style="width:250px;">{{ $rows->address }}</td>
              <td style="padding-left:10px;"><b>Keterangan</b></td>
              <td style="width:30px;text-align:center;">:</td>
              <td>{{ $rows->remark }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>