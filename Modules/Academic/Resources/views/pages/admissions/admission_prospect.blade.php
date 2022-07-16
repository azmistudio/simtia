@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Pendataan Calon Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportProspectiveStudent('excel')">Ekspor Excel</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportProspectiveStudent('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-prospective-student" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fgroup-prospective-student" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
                        label:'Kelompok:',
                        labelWidth:100,
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'group',
                        url: '{{ url('academic/admission/prospective-group/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        queryParams: { is_active: 1, _token: '{{ csrf_token() }}' },
                        columns: [[
                            {field:'department',title:'Departemen',width:120},
                            {field:'admission_id',title:'Proses',width:200},
                            {field:'group',title:'Kelompok',width:110},
                            {field:'quota',title:'Kapasitas/Terisi',width:100},
                        ]],
                    ">
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fregister-prospective-student" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'No. Daftar:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="fname-prospective-student" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterProspectiveStudent({fregister: $('#fregister-prospective-student').val(), fgroup: $('#fgroup-prospective-student').combobox('getValue'), fname: $('#fname-prospective-student').val(), fstudent: 'false'})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-prospective-student').form('reset');filterProspectiveStudent({fstudent: 'false'})">Batal</a>
                </div>
            </form>
            <table id="tb-prospective-student" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'registration_no',width:110,resizeable:true,sortable:true">No. Daftar</th>
                        <th data-options="field:'name',width:180,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-prospective-student" class="panel-top">
            <a id="newProspectiveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newProspectiveStudent()">Baru</a>
            <a id="editProspectiveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editProspectiveStudent()">Ubah</a>
            <a id="saveProspectiveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveProspectiveStudent()">Simpan</a>
            <a id="clearProspectiveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearProspectiveStudent()">Batal</a>
            <a id="deleteProspectiveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteProspectiveStudent()">Hapus</a>
            <a id="pdfProspectiveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfProspectiveStudent()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-prospective-student"></span>Nama Calon Santri: <span id="title-prospective-student"></span></h6>
        </div>
        <div id="page-prospective-student">
            <form id="form-prospective-student-main" method="post">
                <input type="hidden" id="id-prospective-student" name="id" value="-1" />
                <input type="hidden" id="id-admission-prospect" name="admission_id" value="" />
                <div id="tt-prospective-student" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
                    <div title="Data Pribadi" class="pt-3 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <select name="prospect_group_id" id="ProspectStudentProspectGroupId" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                            label:'<b>*</b>Kelompok:',
                                            labelWidth:'125px',
                                            panelWidth: 570,
                                            idField: 'id',
                                            textField: 'group',
                                            fitColumns:true,
                                            columns: [[
                                                {field:'department',title:'Departemen',width:120},
                                                {field:'admission_id',title:'Proses',width:200},
                                                {field:'group',title:'Kelompok',width:110},
                                                {field:'quota',title:'Kapasitas/Terisi',width:100},
                                            ]],
                                        ">
                                        </select>
                                        <span class="mr-2"></span>
                                        <input name="child_no" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="10" data-options="label:'<b>*</b>Anak ke:',labelWidth:'125px',min:1" />
                                        <span class="mr-1"></span>
                                        <input name="child_brother" class="easyui-numberspinner" style="width:90px;height:22px;" tabindex="11" data-options="label:'dari:',labelWidth:'40px',min:0" />
                                        <span class="mr-1">saudara</span>
                                        <span class="mr-2"></span>
                                        <div style="position:absolute;left: 835px;top: 0;">
                                            <fieldset style="width:148px;margin-top:-7px;">
                                                <legend>Foto:</legend>
                                                <input name="photo" id="ProspectStudentPhoto" class="easyui-filebox" data-options="prompt:'Gambar',buttonText:'Pilih',accept:'image/*'" style="width:100%">
                                                <div class="mt-1 mb-1 img-preview">
                                                    <img id="ProspectStudentImgPreview" src="{{ asset('img/img-preview.png') }}" style="display:block;margin:auto;padding:auto;object-fit:cover;height:125px;width:125px;">
                                                </div>
                                                <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Delete'" onclick="clearPreview('ProspectStudentPhoto','ProspectStudentImgPreview')" style="width:125px;">Hapus</a>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <input name="name" id="ProspectStudentName" class="easyui-textbox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <select name="child_status" class="easyui-combobox" style="width:335px;height:22px;" tabindex="12" data-options="label:'<b>*</b>Status Anak:',labelWidth:'125px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($child_status as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="surname" class="easyui-textbox" style="width:335px;height:22px;" tabindex="2" data-options="label:'Panggilan:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="child_brother_sum" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="13" data-options="label:'Saudara Kandung:',labelWidth:'125px',min:0" />
                                        <span class="mr-2">orang</span>
                                    </div>
                                    <div class="mb-1">
                                        <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Jenis Kelamin:</label>
                                        <input name="gender" class="easyui-radiobutton" value="1" data-options="label:'Laki-Laki',labelPosition:'after'" checked="checked" />
                                        <input name="gender" class="easyui-radiobutton" value="2" data-options="label:'Perempuan',labelPosition:'after'" />
                                        <span class="mr-2" style="margin-left:.5em;"></span>
                                        <input name="child_step_sum" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="14" data-options="label:'Saudara Tiri:',labelWidth:'125px',min:0" />
                                        <span class="mr-2">orang</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="pob" class="easyui-textbox" style="width:335px;height:22px;" tabindex="3" data-options="label:'<b>*</b>Tempat Lahir:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="distance" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="15" data-options="label:'Jarak ke Pondok:',labelWidth:'125px',min:0" />
                                        <span class="mr-2">km</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="dob" class="easyui-datebox" style="width:250px;height:22px;" tabindex="4" data-options="label:'<b>*</b>Tanggal Lahir:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                                        <span class="mr-2" style="margin-left:6.1em;"></span>
                                        <input name="phone" class="easyui-textbox" style="width:335px;height:22px;" tabindex="16" data-options="label:'Telpon:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <select id="ProspectStudentTribeId" name="tribe" class="easyui-combobox" style="width:273px;height:22px;" tabindex="5" data-options="label:'<b>*</b>Suku:',labelWidth:'125px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($tribes as $tribe)
                                            <option value="{{ $tribe->id }}">{{ $tribe->name }}</option>
                                            @endforeach
                                        </select>
                                        <a class="easyui-linkbutton small-btn" onclick="tribeDialog()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Add"></i></a>
                                        <a class="easyui-linkbutton small-btn" onclick="reloadStudentTribe('ProspectStudentTribeId')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                                        <span class="mr-2" ></span>
                                        <input name="mobile" class="easyui-textbox" style="width:335px;height:22px;" tabindex="17" data-options="label:'Handphone:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="student_status" class="easyui-combobox" style="width:335px;height:22px;" tabindex="6" data-options="label:'<b>*</b>Status Mukim:',labelWidth:'125px',labelPosition:'before',panelHeight:68,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($student_status as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <input name="email" class="easyui-textbox" style="width:335px;height:22px;" tabindex="18" data-options="label:'Email:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="economic" class="easyui-combobox" style="width:335px;height:22px;" tabindex="7" data-options="label:'<b>*</b>Kondisi:',labelWidth:'125px',labelPosition:'before',panelHeight:68,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($economics as $economic)
                                            <option value="{{ $economic->id }}">{{ $economic->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <input name="hobby" class="easyui-textbox" style="width:335px;height:22px;" tabindex="19" data-options="label:'Hobi:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Warga Negara:</label>
                                        <input name="citizen" class="easyui-radiobutton" value="1" data-options="label:'WNI',labelPosition:'after'" checked="checked" />
                                        <input name="citizen" class="easyui-radiobutton" value="2" data-options="label:'WNA',labelPosition:'after'" />
                                        <span class="mr-2" style="margin-left:.5em;"></span>
                                        
                                    </div>
                                    <div class="mb-1">
                                        <input name="postal_code" class="easyui-numberspinner" style="width:250px;height:22px;" tabindex="8" data-options="label:'Kode Pos:',labelWidth:'125px',min:1" />
                                        <span class="mr-5" style="margin-left:2.8rem;"></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="">
                                        <input name="address" id="address" class="easyui-textbox" style="width:681px;height:22px;" tabindex="9" data-options="label:'<b>*</b>Alamat:',labelWidth:'125px',multiline:false" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Data Orang Tua" class="pt-3 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <span style="position: relative;left: 195px;"><strong><u>Data Ayah</u></strong></span>
                                        <span style="position: relative;left: 350px;"><strong><u>Data Ibu</u></strong></span>
                                        <span style="position: relative;left: 507px;"><strong><u>Data Wali</u></strong></span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="father" class="easyui-textbox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother" class="easyui-textbox" style="width:210px;height:22px;" tabindex="2" data-options="" />
                                        <span class="mr-2"></span>
                                        <input name="parent_guardian" class="easyui-textbox" style="width:210px;height:22px;" data-options="" />
                                    </div>
                                    <div class="mb-1">
                                        <span style="margin-left:7.8rem;"></span>
                                        <input name="is_father_died" class="easyui-checkbox" value="2" style="height:22px;" tabindex="3" data-options="label:'Almarhum',labelWidth:'125px',labelPosition:'after'" />
                                        <span class="mr-5" style="margin-left:1.6rem;"></span>
                                        <input name="is_mother_died" class="easyui-checkbox" value="2" style="height:22px;" tabindex="4" data-options="label:'Almarhumah',labelWidth:'125px',labelPosition:'after'" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="father_status" class="easyui-combobox" style="width:335px;height:22px;" tabindex="5" data-options="label:'<b>*</b>Status Orang Tua:',labelWidth:'125px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($parent_status as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <select name="mother_status" class="easyui-combobox" style="width:210px;height:22px;" tabindex="6" data-options="labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($parent_status as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_pob" class="easyui-textbox" style="width:335px;height:22px;" tabindex="7" data-options="label:'<b>*</b>Tempat Lahir:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_pob" class="easyui-textbox" style="width:210px;height:22px;" tabindex="8" data-options="" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_dob" class="easyui-datebox" style="width:250px;height:22px;" tabindex="9" data-options="label:'<b>*</b>Tanggal Lahir:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                                        <span class="mr-5" style="margin-left:2.8rem;"></span>
                                        <input name="mother_dob" class="easyui-datebox" style="width:125px;height:22px;" tabindex="10" data-options="formatter:dateFormatter,parser:dateParser" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="father_education" class="easyui-combobox" style="width:335px;height:22px;" tabindex="11" data-options="label:'<b>*</b>Pendidikan:',labelWidth:'125px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($educations as $education)
                                            <option value="{{ $education->id }}">{{ $education->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <select name="mother_education" class="easyui-combobox" style="width:210px;height:22px;" tabindex="12" data-options="labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($educations as $education)
                                            <option value="{{ $education->id }}">{{ $education->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <select name="father_job" class="easyui-combobox" style="width:335px;height:22px;" tabindex="13" data-options="label:'<b>*</b>Pekerjaan:',labelWidth:'125px',labelPosition:'before',panelHeight:115,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <select name="mother_job" class="easyui-combobox" style="width:210px;height:22px;" tabindex="14" data-options="labelPosition:'before',panelHeight:115,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_income" class="easyui-numberbox" style="width:335px;height:22px;" tabindex="15" data-options="label:'Penghasilan:',labelWidth:'125px',precision:2,groupSeparator:',',decimalSeparator:'.',prefix:'Rp'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_income" class="easyui-numberbox" style="width:210px;height:22px;" tabindex="16" data-options="precision:2,groupSeparator:',',decimalSeparator:'.',prefix:'Rp'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_email" class="easyui-textbox" style="width:335px;height:22px;" tabindex="17" data-options="label:'Email:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_email" class="easyui-textbox" style="width:210px;height:22px;" tabindex="18" data-options="" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_mobile" class="easyui-textbox" style="width:335px;height:22px;" tabindex="19" data-options="label:'Handphone:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_mobile" class="easyui-textbox" style="width:210px;height:22px;" tabindex="20" data-options="" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="parent_address" id="ProspectStudentParentAddress" class="easyui-textbox" tabindex="21" style="width:557px;height:22px;" data-options="label:'Alamat:',labelWidth:'125px'" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Riwayat Kesehatan" class="pt-3 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <select name="blood" class="easyui-combobox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Golongan Darah:',labelWidth:'125px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($bloods as $blood)
                                            <option value="{{ $blood->id }}">{{ $blood->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="weight" class="easyui-numberspinner" style="width:200px;height:22px;" tabindex="2" data-options="label:'Berat:',labelWidth:'125px',min:0" />
                                        <span class="mr-2">kg</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="height" class="easyui-numberspinner" style="width:200px;height:22px;" tabindex="3" data-options="label:'Tinggi:',labelWidth:'125px',min:0" />
                                        <span class="mr-2">cm</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="medical" class="easyui-textbox" style="width:500px;height:150px;" tabindex="4" data-options="label:'Riwayat Penyakit:',labelWidth:'125px',multiline:true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Data Lainnya" class="pt-3 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <span><strong>Data Sumbangan</strong></span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="donation_1" class="easyui-numberbox" style="width:335px;height:22px;" data-options="label:'Sumbangan #1:',labelWidth:'125px',precision:2,groupSeparator:',',decimalSeparator:'.',prefix:'Rp'" />
                                        <span class="mr-2">( <span id="dn01" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-3">
                                        <input name="donation_2" class="easyui-numberbox" style="width:335px;height:22px;" data-options="label:'Sumbangan #2:',labelWidth:'125px',precision:2,groupSeparator:',',decimalSeparator:'.',prefix:'Rp'" />
                                        <span class="mr-2">( <span id="dn02" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-2">
                                        <span><strong>Data Nilai Ujian</strong></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-1">
                                        <input name="exam_01" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #1:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex01" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_02" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #2:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex02" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_03" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #3:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex03" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_04" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #4:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex04" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_05" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #5:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex05" class="config-code"></span> )</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-1">
                                        <input name="exam_06" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #6:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex06" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_07" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #7:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex07" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_08" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #8:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex08" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_09" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #9:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex09" class="config-code"></span> )</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="exam_10" class="easyui-numberbox" style="width:200px;height:22px;" data-options="label:'Ujian #10:',labelWidth:'125px',precision:2,decimalSeparator:'.'" />
                                        <span class="mr-2">( <span id="ex10" class="config-code"></span> )</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Data Kolom Tambahan" class="pt-3 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div id="ProspectStudentColumns"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionProspectiveStudent = document.getElementById("menu-act-prospective-student").getElementsByTagName("a")
    var markProspectiveStudent = document.getElementById("mark-prospective-student")
    var titleProspectiveStudent = document.getElementById("title-prospective-student")
    var idProspectiveStudent = document.getElementById("id-prospective-student")
    var tabsProspectiveStudent = $("#tt-prospective-student")
    var dgProspectiveStudent = $("#tb-prospective-student")
    $(function () {
        sessionStorage.formPSB_Calon = "init"
        dgProspectiveStudent.datagrid({
            url: "{{ url('academic/admission/prospective-student/data') }}",
            queryParams: { _token: "{{ csrf_token() }}", is_student: "false" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPSB_Calon == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleProspectiveStudent.innerText = row.name
                    actionButtonProspectiveStudent("active",[2,3])
                    $.get("{{ url('academic/admission/column/view') }}" + "/" + row.department_id, function(response) {
                        $("#ProspectStudentColumns").html(response)
                        $(".cbox").combobox({
                            labelWidth: '150px',
                            labelPosition: 'before',
                            panelHeight: 112
                        })      
                        $(".tbox").textbox({
                            labelWidth: '150px'
                        })      
                    })
                    $("#form-prospective-student-main").form("reset")
                    $("#form-prospective-student-main").form("load", "{{ url('academic/admission/prospective-student/show') }}" + "/" + row.id)
                    $("#ProspectStudentPhoto").filebox("setText", row.photo)
                    if (row.photo != "") {
                        $("#ProspectStudentImgPreview").attr("src", "/storage/uploads/student/" + row.photo)
                    } else {
                        clearPreview("ProspectStudentPhoto","ProspectStudentImgPreview")
                    }
                    $.getJSON("{{ url('academic/admission/config/getbyadmission') }}" + "/" + row.id_admission, function(response) {
                        $("#dn01").text(response.donate_code_1)
                        $("#dn02").text(response.donate_code_2)
                        $("#ex01").text(response.exam_code_01)
                        $("#ex02").text(response.exam_code_02)
                        $("#ex03").text(response.exam_code_03)
                        $("#ex04").text(response.exam_code_04)
                        $("#ex05").text(response.exam_code_05)
                        $("#ex06").text(response.exam_code_06)
                        $("#ex07").text(response.exam_code_07)
                        $("#ex08").text(response.exam_code_08)
                        $("#ex09").text(response.exam_code_09)
                        $("#ex10").text(response.exam_code_10)
                    })
                    $("#tt-prospective-student").waitMe("hide")
                }
            }
        })
        dgProspectiveStudent.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgProspectiveStudent.datagrid('getPager').pagination())
        actionButtonProspectiveStudent("{{ $ViewType }}", [])
        $("#ProspectStudentName").textbox("textbox").bind("keyup", function (e) {
            titleProspectiveStudent.innerText = $(this).val()
        })
        $("#address").textbox("textbox").bind("keyup", function (e) {
            $("#ProspectStudentParentAddress").textbox('setValue', $(this).val())
        })
        $("#ProspectStudentPhoto").filebox({
            onChange: function(newValue, oldValue) {
                previewFile('ProspectStudentPhoto','ProspectStudentImgPreview')
            }
        })
        $("#ProspectStudentProspectGroupId").combogrid({
            url: '{{ url('academic/admission/prospective-group/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { is_active: 1, _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                if (row.capacity == row.occupied) {
                    $.messager.alert('Peringatan', 'Kuota Kelompok Pendaftaran ' + row.group + ' sudah terpenuhi, silahkan pilih Kelompok lainnya.', 'error')
                    $("#ProspectStudentProspectGroupId").combogrid('clear')    
                } else {
                    $('#id-admission-prospect').val(row.admission)
                    $.getJSON("{{ url('academic/admission/config/getbyadmission') }}" + "/" + row.admission, { _token: '{{ csrf_token() }}' }, function(response) {
                        $("#dn01").text(response.donate_code_1)
                        $("#dn02").text(response.donate_code_2)
                        $("#ex01").text(response.exam_code_01)
                        $("#ex02").text(response.exam_code_02)
                        $("#ex03").text(response.exam_code_03)
                        $("#ex04").text(response.exam_code_04)
                        $("#ex05").text(response.exam_code_05)
                        $("#ex06").text(response.exam_code_06)
                        $("#ex07").text(response.exam_code_07)
                        $("#ex08").text(response.exam_code_08)
                        $("#ex09").text(response.exam_code_09)
                        $("#ex10").text(response.exam_code_10)        
                    })
                    $.get("{{ url('academic/admission/column/view') }}" + "/" + row.department_id, function(response) {
                        $("#ProspectStudentColumns").html(response)
                        $(".cbox").combobox({
                            labelWidth: '150px',
                            labelPosition: 'before',
                            panelHeight: 112
                        })      
                        $(".tbox").textbox({
                            labelWidth: '150px'
                        })      
                    })
                }
                $("#ProspectStudentProspectGroupId").combogrid('hidePanel')
            }
        })
        $("#form-prospective-student-main").form({
            onLoadSuccess: function(data) {
                if (typeof data.columns !== 'undefined') {
                    for (var i = 0; i < data.columns.length; i++) {
                        let col_id = data.columns[i].column_id
                        if (data.columns[i].type == 1) {
                            $("#col"+col_id).textbox('setValue', data.columns[i].values)
                        } else {
                            $("#col"+col_id).combobox('setValue', data.columns[i].values)
                        }
                    }
                }
            }
        })
        $("#tt-prospective-student").waitMe({effect:"none"})
    })
    function filterProspectiveStudent(params) {
        if (Object.keys(params).length > 0) {
            dgProspectiveStudent.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgProspectiveStudent.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newProspectiveStudent() {
        sessionStorage.formPSB_Calon = "active"
        $("#form-prospective-student-main").form("reset")
        actionButtonProspectiveStudent("active", [0,1,4,5])
        markProspectiveStudent.innerText = "*"
        titleProspectiveStudent.innerText = ""
        idProspectiveStudent.value = "-1"
        clearPreview("ProspectStudentPhoto","ProspectStudentImgPreview")
        $("#ProspectStudentName").textbox('textbox').focus()
        $(".config-code").text("")
        tabsProspectiveStudent.tabs("select", 0)
        $("#tt-prospective-student").waitMe("hide")
    }
    function editProspectiveStudent() {
        sessionStorage.formPSB_Calon = "active"
        markProspectiveStudent.innerText = "*"
        actionButtonProspectiveStudent("active", [0,1,4])
    }
    function saveProspectiveStudent() {
        if (sessionStorage.formPSB_Calon == "active") {
            ajaxProspectiveStudent("academic/admission/prospective-student/store")
        }
    }
    function deleteProspectiveStudent() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Calon Santri terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/admission/prospective-student/destroy') }}" +"/"+idProspectiveStudent.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxProspectiveStudentResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function pdfProspectiveStudent() {
        if (idProspectiveStudent.value != -1) {
            exportDocument("{{ url('academic/admission/prospective-student/print') }}", { id: idProspectiveStudent.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxProspectiveStudent(route) {
        $("#form-prospective-student-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-prospective-student").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxProspectiveStudentResponse(response)
                $("#page-prospective-student").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-prospective-student").waitMe("hide")
            }
        })
        return false
    }
    function ajaxProspectiveStudentResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearProspectiveStudent()
            $("#tb-prospective-student").datagrid("reload")
            $("#fgroup-prospective-student").combogrid("grid").datagrid("reload")
            $("#ProspectStudentProspectGroupId").combogrid("grid").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearProspectiveStudent() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearProspectiveStudent()
            }
        })
    }
    function actionButtonProspectiveStudent(viewType, idxArray) {
        for (var i = 0; i < menuActionProspectiveStudent.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionProspectiveStudent[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionProspectiveStudent[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionProspectiveStudent[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionProspectiveStudent[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearProspectiveStudent() {
        sessionStorage.formPSB_Calon = "init"
        $("#form-prospective-student-main").form("reset")
        actionButtonProspectiveStudent("init", [])
        titleProspectiveStudent.innerText = ""
        markProspectiveStudent.innerText = ""
        idProspectiveStudent.value = "-1"
        clearPreview("ProspectStudentPhoto","ProspectStudentImgPreview")
        $(".config-code").text("")
        tabsProspectiveStudent.tabs("select", 0)
        $("#tt-prospective-student").waitMe({effect:"none"})
    }
    function exportProspectiveStudent(document) {
        var arrays = []
        var dg = $("#tb-prospective-student").datagrid('getData')
        for (var i = 0; i < dg.rows.length; i++) {
            arrays.push(dg.rows[i].department_id)
        }
        if (toFindDuplicate(arrays).length > 1) {
            $.messager.alert('Peringatan', 'Pilih salah satu Kelompok', 'error')
        } else {
            if (dg.total > 0) {
                exportDocument("{{ url('academic/admission/prospective-student/export-') }}" + document,dg.rows,"Ekspor data Calon Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
            }
        }
    }
    function reloadStudentTribe(id) {
        $('#'+id).combobox('reload','{{ url("reference/list") }}' + "/hr_tribe")
    }
</script>