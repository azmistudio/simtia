@inject('reference', 'Modules\Academic\Http\Controllers\AdmissionProspectController')
@php
    $photo = !empty($model->photo) ? storage_path('app/public/uploads/student/'.$model->photo) : public_path('img/default-user.png');
    $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
    <head>
        <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Calon Santri</title>
        <style type="text/css">
            body {margin: 0;padding: 0;font-size: 11px;font-family: "Segoe UI", "Open Sans", serif !important;}
            #header {top: 0;margin-bottom: 10px;background-color: #fff;}
            .text-left {text-align: left;}
            .text-center {text-align: center;}
            .text-right {text-align: right;}
            .break {page-break-before: avoid;}
            .must-break {page-break-before: always;}
            .center {margin: 0 auto;position: relative;display: flex;justify-content: center;}
            table.no-border, table.no-border th, table.no-border td {border: none;}
            table {border-collapse: collapse;border: 1px solid #000;}
            th, td {border-top: 1px solid #000;}
        </style>
    </head>
    <body style="">
        <div id="header">
            <table class="table no-border" style="width:100%;">
                <tbody>
                    <tr>
                        <th rowspan="2" style="width:10%;"><img src="file:///{{ $logo }}" height="80px" /></th>
                        <td><b>{{ strtoupper(Session::get('institute')) }}</b></td>
                    </tr>
                    <tr>
                        <td style="font-size:11px;">
                            {{ $profile['address'] }}<br/>
                            Telp. {{ $profile['phone'] }} - Fax. {{ $profile['fax'] }}<br/>
                            Website: {{ $profile['web'] }} - Email: {{ $profile['email'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr/>
        <div id="body">
            <br/>
            <div class="text-center" style="font-size:16px;"><b>Data Calon Santri</b></div>
            <br/>
            <br/>
            <div>
                <table class="table no-border">
                    <tbody style="font-size:13px;font-weight: 700;">
                        <tr>
                            <td>Departemen</td>
                            <td style="width:30px;text-align:center;">:</td>
                            <td>{{ $model->getProspectGroup->getAdmission->getDepartment->name }}</td>
                        </tr>
                        <tr>
                            <td>Proses Penerimaan</td>
                            <td style="width:30px;text-align:center;">:</td>
                            <td>{{ $model->getProspectGroup->getAdmission->name }}</td>
                        </tr>
                        <tr>
                            <td>Kelompok Calon Santri</td>
                            <td style="width:30px;text-align:center;">:</td>
                            <td>{{ $model->getProspectGroup->group }}</td>
                        </tr>
                        <tr>
                            <td>No. Pendaftaran</td>
                            <td style="width:30px;text-align:center;">:</td>
                            <td>{{ $model->registration_no }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br/>
            <div class="">
                <table style="width:100%;">
                    <tbody style="font-size:13px;">
                        <tr>
                            <td style="width:20px;"><b>A.</b></td>
                            <td colspan="4"><b>DATA PRIBADI</b></td>
                            <th rowspan="6" colspan="2" style="border-left: solid 1px #000;"><img src="file:///{{ $photo }}" height="100px" /></th>
                            <th rowspan="6" colspan="2">
                                Tanda Tangan
                                <br/>
                                <br/>
                                <br/>
                                <br/>
                                ( {{ $model->name }} )
                            </th>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">1.</td>
                            <td style="width:150px;">Nama Lengkap</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->name }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">2.</td>
                            <td style="width:150px;">Nama Panggilan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->surname }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">3.</td>
                            <td style="width:150px;">Jenis Kelamin</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getGender()[$model->gender] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">4.</td>
                            <td style="width:150px;">Tempat, Tanggal Lahir</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->pob .', '. $model->dob->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">5.</td>
                            <td style="width:150px;">Agama</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>Islam</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">6.</td>
                            <td style="width:150px;">Kewarganegaraan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getCitizen()[$model->citizen] }}</td>
                            <td style="width:20px;border-left: solid 1px #000;padding-left: 10px;">15.</td>
                            <td style="width:15%;">Telpon</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:20%;">{{ $model->phone }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">7.</td>
                            <td style="width:150px;">Anak ke</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->child_no }} dari {{ $model->child_brother }} bersaudara</td>
                            <td style="width:20px;border-left: solid 1px #000;padding-left: 10px;">16.</td>
                            <td>Email</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->email }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">8.</td>
                            <td style="width:150px;">Status Anak</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getReferences('hr_child_status')[$model->child_status] }}</td>
                            <td style="width:20px;border-left: solid 1px #000;padding-left: 10px;">17.</td>
                            <td>Handphone</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->mobile }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">9.</td>
                            <td style="width:150px;">Jumlah Saudara Kandung</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->child_brother_sum }}</td>
                            <td style="width:20px;border-left: solid 1px #000;padding-left: 10px;">18.</td>
                            <td>Jarak ke Ma'had</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->distance }} km</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">10.</td>
                            <td style="width:150px;">Jumlah Saudara Tiri</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->child_step_sum }}</td>
                            <td style="width:20px;border-left: solid 1px #000;padding-left: 10px;">19.</td>
                            <td>Kode Pos</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->postal_code }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">11.</td>
                            <td style="width:150px;">Kondisi Ekonomi</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getReferences('hr_economic')[$model->economic] }}</td>
                            <td style="width:20px;border-left: solid 1px #000;padding-left: 10px;">20.</td>
                            <td>Alamat</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">12.</td>
                            <td style="width:150px;">Status Santri</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getReferences('hr_student_status')[$model->student_status] }}</td>
                            <td colspan="4" rowspan="3" style="vertical-align: baseline;border-left: solid 1px #000;border-top:0 !important;padding-left: 10px;">{{ $model->address }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">13.</td>
                            <td style="width:150px;">Suku</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getReferences('hr_tribe')[$model->tribe] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">14.</td>
                            <td style="width:150px;">Hobi</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->hobby }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br/>
            <div class="">
                <table style="width:100%;">
                    <tbody style="font-size:13px;">
                        <tr>
                            <td style="width:20px;"><b>B.</b></td>
                            <td colspan="4"><b>DATA KESEHATAN</b></td>
                        </tr>
                        <tr>
                            <td style="width:20px;"></td>
                            <td style="width:20px;">21.</td>
                            <td style="width:150px;">Berat Badan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->weight }} kg</td>
                        </tr>
                        <tr>
                            <td style="width:20px;"></td>
                            <td style="width:20px;">22.</td>
                            <td style="width:150px;">Tinggi Badan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->height }} cm</td>
                        </tr>
                        <tr>
                            <td style="width:20px;"></td>
                            <td style="width:20px;">23.</td>
                            <td style="width:150px;">Golongan Darah</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $reference->getReferences('hr_blood')[$model->blood] }}</td>
                        </tr>
                        <tr>
                            <td style="width:20px;"></td>
                            <td style="width:20px;">24.</td>
                            <td style="width:150px;">Riwayat Penyakit</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td>{{ $model->medical }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br/>
            <div class="">
                <table style="width:100%;">
                    <tbody style="font-size:13px;">
                        <tr>
                            <td style="width:20px;"><b>C.</b></td>
                            <td colspan="3"><b>DATA ORANG TUA</b></td>
                            <td class="text-center"><b>AYAH</b></td>
                            <td class="text-center" style="border-left: solid 1px #000;"><b>IBU</b></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">29.</td>
                            <td style="width:150px;">Nama</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ $model->is_father_died == 2 ?  $model->father . ' (Alm.)' : $model->father }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ $model->is_mother_died == 2 ?  ucwords($model->mother) . ' (Almh.)' : ucwords($model->mother) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">30.</td>
                            <td style="width:150px;">Status Orang Tua</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ $reference->getReferences('hr_parent_status')[$model->father_status] }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ $reference->getReferences('hr_parent_status')[$model->mother_status] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">31.</td>
                            <td style="width:150px;">Tempat, Tanggal Lahir</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ ucwords($model->father_pob) .', '. $model->father_dob->format('d/m/Y') }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ ucwords($model->mother_pob) .', '. $model->mother_dob->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">32.</td>
                            <td style="width:150px;">Pendidikan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ $reference->getReferences('hr_education')[$model->father_education] }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ $reference->getReferences('hr_education')[$model->mother_education] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">33.</td>
                            <td style="width:150px;">Pekerjaan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ $reference->getReferences('hr_job')[$model->father_job] }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ $reference->getReferences('hr_job')[$model->mother_job] }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">34.</td>
                            <td style="width:150px;">Penghasilan</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">Rp{{ number_format($model->father_income,2) }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">Rp{{ number_format($model->mother_income,2) }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">35.</td>
                            <td style="width:150px;">Email</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ $model->father_email }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ $model->mother_email }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">36.</td>
                            <td style="width:150px;">No. Handphone</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td style="width:35%;">{{ $model->father_mobile }}</td>
                            <td style="border-left: solid 1px #000;padding-left: 10px;">{{ $model->mother_mobile }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;">37.</td>
                            <td style="width:150px;">Nama Wali</td>
                            <td style="width:20px;text-align:center;">:</td>
                            <td colspan="2">{{ $model->parent_guardian }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:20px;vertical-align:baseline;">38.</td>
                            <td style="width:150px;vertical-align:baseline;">Alamat</td>
                            <td style="width:20px;text-align:center;vertical-align:baseline;">:</td>
                            <td colspan="2" style="vertical-align:baseline;">{{ $model->parent_address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br/>
            @if (isset($columns))
            @php
                $total = count($columns);
                $x = 38;
            @endphp
                <div class="">
                    <table style="width:100%;">
                        <tbody style="font-size:13px;">
                            <tr>
                                <td style="width:20px;"><b>D.</b></td>
                                <td colspan="4"><b>DATA KOLOM TAMBAHAN</b></td>
                            </tr>
                            @foreach ($columns as $col)
                            <tr>
                                <td style="width:20px;"></td>
                                <td style="width:20px;">{{ $x + 1 }}.</td>
                                <td style="width:150px;">{{ ucfirst($col->getColumn->name) }}</td>
                                <td style="width:20px;text-align:center;">:</td>
                                <td style="">{{ $col->type == 1 ? ucfirst($col->values) : ucfirst($col->getColumnOption->name) }}</td>
                            </tr>
                            @php $x++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </body>
</html>