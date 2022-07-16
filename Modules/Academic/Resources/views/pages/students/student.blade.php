@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
    $TabHeight = $InnerHeight - 251 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Pendataan Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportStudent('excel')">Ekspor Excel</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportStudent('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-student" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fclass-student" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
                        label:'Kelas:',
                        labelWidth:100,
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'class',
                        url: '{{ url('academic/class/student/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        queryParams: { _token: '{{ csrf_token() }}' },
                        columns: [[
                            {field:'department',title:'Departemen',width:150},
                            {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                            {field:'grade',title:'Tingkat',width:80,align:'center'},
                            {field:'class',title:'Kelas',width:120},
                            {field:'capacity',title:'Kapasitas/Terisi',width:120},
                        ]],
                    ">
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fnis-student" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'NIS:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="fname-student" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterStudent({fnis: $('#fnis-student').val(), fclass: $('#fclass-student').combobox('getValue'), fname: $('#fname-student').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-student').form('reset');filterStudent({})">Batal</a>
                </div>
            </form>
            <table id="tb-students" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'student_no',width:80,resizeable:true,sortable:true">NIS</th>
                        <th data-options="field:'name',width:170,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-student" class="panel-top">
            <a id="editStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editStudent()">Ubah</a>
            <a id="saveStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveStudent()">Simpan</a>
            <a id="clearStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearStudent()">Batal</a>
            <a id="deleteStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteStudent()">Hapus</a>
            <a id="pdfStudent" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfStudent()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-student"></span>Nama Santri: <span id="title-student"></span></h6>
        </div>
        <div id="page-student">
            <form id="form-student-main" method="post">
                <input type="hidden" id="id-student" name="id" value="-1" />
                <input type="hidden" id="id-old-class" value="" />
                <div id="tt-student" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
                    <div title="Data Pribadi" class="pt-2 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="StudentDept" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                        <span class="mr-2"></span>
                                        <select id="StudentTribeId" name="tribe" class="easyui-combobox" style="width:273px;height:22px;" tabindex="8" data-options="label:'<b>*</b>Suku:',labelWidth:'125px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($tribes as $tribe)
                                            <option value="{{ $tribe->id }}">{{ $tribe->name }}</option>
                                            @endforeach
                                        </select>
                                        <a class="easyui-linkbutton small-btn" onclick="tribeDialog()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Add"></i></a>
                                        <a class="easyui-linkbutton small-btn" onclick="reloadStudentTribe('StudentTribeId')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                                        <span class="mr-2"></span>
                                        <div style="position:absolute;left: 835px;top: 0;">
                                            <fieldset style="width:148px;margin-top:-7px;">
                                                <legend>Foto:</legend>
                                                <input name="photo" id="photo-student" class="easyui-filebox" data-options="prompt:'Gambar',buttonText:'Pilih',accept:'image/*'" style="width:100%">
                                                <div class="mt-1 mb-1 img-preview">
                                                    <img id="preview-img-student" src="{{ asset('img/img-preview.png') }}" style="display:block;margin:auto;padding:auto;object-fit:cover;height:125px;width:125px;">
                                                </div>
                                                <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Delete'" onclick="clearPreview('photo-student','preview-img-student')" style="width:125px;">Hapus</a>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="StudentSchoolYear" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                                        <span class="mr-2"></span>
                                        <select name="student_status" class="easyui-combobox" style="width:335px;height:22px;" tabindex="9" data-options="label:'<b>*</b>Status:',labelWidth:'125px',labelPosition:'before',panelHeight:68,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($student_status as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="StudentGrade" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                                        <span class="mr-2"></span>
                                        <select name="economic" class="easyui-combobox" style="width:335px;height:22px;" tabindex="10" data-options="label:'<b>*</b>Kondisi:',labelWidth:'125px',labelPosition:'before',panelHeight:68,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($economics as $economic)
                                            <option value="{{ $economic->id }}">{{ $economic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <select name="class_id" id="StudentClass" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                            label:'<b>*</b>Kelas:',
                                            labelWidth:'125px',
                                            panelWidth: 570,
                                            idField: 'id',
                                            textField: 'class',
                                            fitColumns:true,
                                            columns: [[
                                                {field:'department',title:'Departemen',width:150},
                                                {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                                {field:'grade',title:'Tingkat',width:80,align:'center'},
                                                {field:'class',title:'Kelas',width:120},
                                                {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                            ]],
                                        ">
                                        </select>
                                        <span class="mr-2"></span>
                                        <input name="child_no" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="11" data-options="label:'<b>*</b>Anak ke:',labelWidth:'125px'" />
                                        <span class="mr-1"></span>
                                        <input name="child_brother" class="easyui-numberspinner" style="width:90px;height:22px;" tabindex="12" data-options="label:'dari:',labelWidth:'40px'" />
                                        <span class="mr-1">saudara</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="student_no" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'NIS:',labelWidth:'125px',readonly:true" />
                                        <span class="mr-2"></span>
                                        <select name="child_status" class="easyui-combobox" style="width:335px;height:22px;" tabindex="13" data-options="label:'<b>*</b>Status Anak:',labelWidth:'125px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($child_status as $child)
                                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="name" id="StudentName" class="easyui-textbox" style="width:335px;height:22px;" tabindex="2" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="child_brother_sum" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="14" data-options="label:'Saudara Kandung:',labelWidth:'125px'" />
                                        <span class="mr-2">orang</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="surname" class="easyui-textbox" style="width:335px;height:22px;" tabindex="3" data-options="label:'Panggilan:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="child_step_sum" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="15" data-options="label:'Saudara Tiri:',labelWidth:'125px'" />
                                        <span class="mr-2">orang</span>
                                    </div>
                                    <div class="mb-1">
                                        <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Jenis Kelamin:</label>
                                        <input name="gender" class="easyui-radiobutton" value="1" data-options="label:'Laki-Laki',labelPosition:'after'" checked="checked" />
                                        <input name="gender" class="easyui-radiobutton" value="2" data-options="label:'Perempuan',labelPosition:'after'" />
                                        <span class="mr-2" style="margin-left: .5rem;"></span>
                                        <input name="distance" class="easyui-numberspinner" style="width:180px;height:22px;" tabindex="16" data-options="label:'Jarak ke Ma`had:',labelWidth:'125px'" />
                                        <span class="mr-2">km</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="pob" class="easyui-textbox" style="width:335px;height:22px;" tabindex="4" data-options="label:'<b>*</b>Tempat Lahir:',labelWidth:'125px'" />
                                        <span class="mr-2" style="margin-left: 1px;"></span>
                                        <input name="phone" class="easyui-textbox" style="width:335px;height:22px;" tabindex="17" data-options="label:'Telpon:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="dob" class="easyui-datebox" style="width:250px;height:22px;" tabindex="5" data-options="label:'<b>*</b>Tanggal Lahir:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                                        <span class="mr-2" style="margin-left:5.4rem;"></span>
                                        <input name="mobile" class="easyui-textbox" style="width:335px;height:22px;" tabindex="22" data-options="label:'Handphone:',labelWidth:'125px'" />
                                        <span class="mr-2" style="margin-left:125px;"></span>
                                        <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif',labelWidth:'125px',labelPosition:'after'" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Warga Negara:</label>
                                        <input name="citizen" class="easyui-radiobutton" value="1" data-options="label:'WNI',labelPosition:'after'" checked="checked" />
                                        <input name="citizen" class="easyui-radiobutton" value="2" data-options="label:'WNA',labelPosition:'after'" />
                                        <span class="mr-2" style="margin-left: .5rem;"></span>
                                        <input name="email" class="easyui-textbox" style="width:335px;height:22px;" tabindex="23" data-options="label:'Email:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="postal_code" class="easyui-numberspinner" style="width:250px;height:22px;" tabindex="6" data-options="label:'Kode Pos:',labelWidth:'125px'" />
                                        <span class="mr-2" style="margin-left:5.4rem;"></span>
                                        <input name="hobby" class="easyui-textbox" style="width:335px;height:22px;" tabindex="24" data-options="label:'Hobi:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="address" id="address" class="easyui-textbox" style="width:683px;height:22px;" tabindex="7" data-options="label:'<b>*</b>Alamat:',labelWidth:'125px'" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Data Orang Tua" class="pt-2 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-2">
                                        <span style="position: relative;left: 195px;"><strong><u>Data Ayah</u></strong></span>
                                        <span style="position: relative;left: 350px;"><strong><u>Data Ibu</u></strong></span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="father" class="easyui-textbox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother" class="easyui-textbox" style="width:210px;height:22px;" tabindex="2" data-options="" />
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
                                        <select name="father_job" class="easyui-combobox" style="width:335px;height:22px;" tabindex="13" data-options="label:'<b>*</b>Pekerjaan:',labelWidth:'125px',labelPosition:'before',panelHeight:90,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <select name="mother_job" class="easyui-combobox" style="width:210px;height:22px;" tabindex="14" data-options="labelPosition:'before',panelHeight:90,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($jobs as $job)
                                            <option value="{{ $job->id }}">{{ $job->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_income" class="easyui-numberbox" style="width:335px;height:22px;" data-options="label:'Penghasilan:',labelWidth:'125px',precision:2,groupSeparator:',',decimalSeparator:'.',prefix:'Rp'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_income" class="easyui-numberbox" style="width:210px;height:22px;" data-options="precision:2,groupSeparator:',',decimalSeparator:'.',prefix:'Rp'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_email" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Email:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_email" class="easyui-textbox" style="width:210px;height:22px;" data-options="" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="father_mobile" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Handphone:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mother_mobile" class="easyui-textbox" style="width:210px;height:22px;" data-options="" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="parent_guardian" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Nama Wali:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="parent_address" id="StudentParentAddress" class="easyui-textbox" style="width:558px;height:22px;" data-options="label:'Alamat:',labelWidth:'125px'" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Riwayat Kesehatan" class="pt-2 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1">
                                        <select name="blood" class="easyui-combobox" style="width:335px;height:22px;" tabindex="10" data-options="label:'Golongan Darah:',labelWidth:'125px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($bloods as $blood)
                                            <option value="{{ $blood->id }}">{{ $blood->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="weight" class="easyui-numberspinner" style="width:200px;height:22px;" tabindex="6" data-options="label:'Berat:',labelWidth:'125px'" />
                                        <span class="mr-2">kg</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="height" class="easyui-numberspinner" style="width:200px;height:22px;" tabindex="6" data-options="label:'Tinggi:',labelWidth:'125px'" />
                                        <span class="mr-2">cm</span>
                                    </div>
                                    <div class="mb-1">
                                        <input name="medical" class="easyui-textbox" style="width:500px;height:150px;" tabindex="12" data-options="label:'Riwayat Penyakit:',labelWidth:'125px',multiline:true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div title="Data Kolom Tambahan" class="pt-2 pb-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div id="StudentColumns"></div>
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
    var menuActionStudent = document.getElementById("menu-act-student").getElementsByTagName("a")
    var markStudent = document.getElementById("mark-student")
    var titleStudent = document.getElementById("title-student")
    var idStudent = document.getElementById("id-student")
    var tabstudent = $("#tt-student")
    var dgStudent = $("#tb-students")
    $(function () {
        sessionStorage.formData_Santri = "init"
        dgStudent.datagrid({
            url: "{{ url('academic/student/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formData_Santri == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleStudent.innerText = row.name
                    actionButtonStudent("active",[1,2])
                    $.get("{{ url('academic/admission/column/view') }}" + "/" + row.department_id, function(response) {
                        $("#StudentColumns").html(response)
                        $(".cbox").combobox({
                            labelWidth: '150px',
                            labelPosition: 'before',
                            panelHeight: 112
                        })      
                        $(".tbox").textbox({
                            labelWidth: '150px'
                        })      
                    })
                    $("#form-student-main").form("reset")
                    $("#StudentDept").textbox("setValue", row.department)
                    $("#StudentSchoolYear").textbox("setValue", row.school_year)
                    $("#StudentGrade").textbox("setValue", row.grade)
                    $("#form-student-main").form("load", "{{ url('academic/student/show') }}" + "/" + row.id)
                    $("#photo-student").filebox("setText", row.photo)
                    if (row.photo != "") {
                        $("#preview-img-student").attr("src", "/storage/uploads/student/" + row.photo)
                    } else {
                        clearPreview("photo-student","preview-img-student")
                    }
                    tabstudent.tabs("select", 0)
                    $("#tt-student").waitMe("hide")
                }
            }
        })
        dgStudent.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgStudent.datagrid('getPager').pagination())
        actionButtonStudent("{{ $ViewType }}", [])
        $("#StudentName").textbox("textbox").bind("keyup", function (e) {
            titleStudent.innerText = $(this).val()
        })
        $("#address").textbox("textbox").bind("keyup", function (e) {
            $("#StudentParentAddress").textbox('setValue', $(this).val())
        })
        $("#photo-student").filebox({
            onChange: function(newValue, oldValue) {
                previewFile('photo-student','preview-img-student')
            }
        })
        $("#form-student-main").form({
            onLoadSuccess: function(data) {
                $("#id-old-class").val(data.class_id)
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
        $("#StudentClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                if (row.id != $("#id-old-class").val()) {
                    params = row.capacity.split("/")
                    if (parseInt(params[0]) == parseInt(params[1])) {
                        $.messager.alert('Peringatan', 'Kuota Kelas ' + row.class + ' sudah terpenuhi, silahkan pilih Kelas lainnya.', 'error')
                        $("#StudentClass").combogrid('clear')    
                    } else {
                        $("#StudentDept").textbox("setValue", row.department)
                        $("#StudentSchoolYear").textbox("setValue", row.school_year)
                        $("#StudentGrade").textbox("setValue", row.grade)
                    }
                }
                $("#StudentClass").combogrid('hidePanel')
            }
        })
        $("#tt-student").waitMe({effect:"none"})
    })
    function filterStudent(params) {
        if (Object.keys(params).length > 0) {
            dgStudent.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgStudent.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function editStudent() {
        sessionStorage.formData_Santri = "active"
        markStudent.innerText = "*"
        actionButtonStudent("active", [0,3,4])
    }
    function saveStudent() {
        if (sessionStorage.formData_Santri == "active") {
            if ( $("#id-old-class").val() != $("#StudentClass").combogrid('getValue') ) {
                $.messager.confirm("Konfirmasi", "Anda akan mengubah data Kelas Santri terpilih (Pindah Kelas), tetap lanjutkan?", function (r) {
                    if (r) {
                        ajaxStudent("academic/student/store")
                    }
                })
            } else {
                ajaxStudent("academic/student/store")
            }
        }
    }
    function deleteStudent() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Santri terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/student/destroy') }}" +"/"+idStudent.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxStudentResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function pdfStudent() {
        if (idStudent.value != -1) {
            exportDocument("{{ url('academic/student/print') }}", { id: idStudent.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxStudent(route) {
        $("#form-student-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-student").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxStudentResponse(response)
                $("#page-student").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-student").waitMe("hide")
            }
        })
        return false
    }
    function ajaxStudentResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearStudent()
            $("#tb-students").datagrid("reload")
            $("#fclass-student").combogrid('grid').datagrid("reload")
            $("#StudentClass").combogrid('grid').datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearStudent() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearStudent()
            }
        })
    }
    function actionButtonStudent(viewType, idxArray) {
        for (var i = 0; i < menuActionStudent.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionStudent[i].id).linkbutton({disabled: true})
            } else {
                $("#" + menuActionStudent[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionStudent[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearStudent() {
        sessionStorage.formData_Santri = "init"
        $("#form-student-main").form("reset")
        actionButtonStudent("init", [])
        titleStudent.innerText = ""
        markStudent.innerText = ""
        idStudent.value = "-1"
        clearPreview("photo-student","preview-img-student")
        $(".config-code").text("")
        $("#id-old-class").val("-1")
        tabstudent.tabs("select", 0)
        $("#tt-student").waitMe({effect:"none"})
    }
    function exportStudent(document) {
        var arrays = []
        var dg = $("#tb-students").datagrid('getData')
        for (var i = 0; i < dg.rows.length; i++) {
            arrays.push(dg.rows[i].department_id)
        }
        if (toFindDuplicate(arrays).length > 1) {
            $.messager.alert('Peringatan', 'Pilih salah satu Departemen', 'error')
        } else {
            if (dg.total > 0) {
                exportDocument("{{ url('academic/student/export-') }}" + document,dg.rows,"Ekspor data Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
            }
        }
    }
    function reloadStudentTribe(id) {
        $('#'+id).combobox('reload','{{ url("reference/list") }}' + "/hr_tribe")
    }
</script>