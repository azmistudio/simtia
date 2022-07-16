@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 361 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Profil Saya</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div id="form-region" class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div id="menu-act-employee-profile" class="panel-top">
            <a id="saveEmployeeProfile" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveEmployeeProfile()">Simpan</a>
        </div>
        <div class="title">
            <h6><span id="mark-employee-profile"></span>Pegawai: {{ $profile->name }}</h6>
        </div>
        <div id="tt-employee-profile" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
            <div title="Umum" class="pt-3">
                <form id="form-employee-profile-main" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="{{ $profile->id }}" />
                <input type="hidden" name="section" value="{{ $profile->section }}" />
                <div class="container-fluid">
                    <div class="row row-cols-auto">
                        <div class="col">
                            <div class="mb-1">
                                <input value="{{ $profile->getSection->name }}" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Bagian:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <select name="marital" id="EmployeeProfileMarital" class="easyui-combobox" style="width:335px;height:22px;" tabindex="7" data-options="label:'<b>*</b>Menikah:',labelWidth:'125px',labelPosition:'before',panelHeight:90">
                                    <option value="">---</option>
                                    <option value="1">Belum Menikah</option>
                                    <option value="2">Sudah Menikah</option>
                                    <option value="3">Janda/Duda</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->employee_id }}" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'NIP:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <select id="EmployeeProfileTribe" name="tribe" class="easyui-combobox" style="width:335px;height:22px;" tabindex="8" data-options="label:'<b>*</b>Suku:',labelWidth:'125px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'name'">
                                    <option value="">---</option>
                                    @foreach ($tribes as $tribe)
                                    <option value="{{ $tribe->id }}">{{ $tribe->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->name }}" name="name" id="nameEmployee" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Nama:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input value="{{ $profile->national_id }}" name="national_id" class="easyui-textbox" style="width:335px;height:22px;" tabindex="9" data-options="label:'<b>*</b>No. Identitas:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->title_first }}" name="title_first" class="easyui-textbox" style="width:335px;height:22px;" tabindex="1" data-options="label:'Gelar Awal:',labelWidth:'125px'" />
                                <span class="mr-2"></span>
                                <input value="{{ $profile->phone }}" name="phone" class="easyui-textbox" style="width:335px;height:22px;" tabindex="10" data-options="label:'No. Telpon:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->title_end }}" name="title_end" class="easyui-textbox" style="width:335px;height:22px;" tabindex="2" data-options="label:'Gelar Akhir:',labelWidth:'125px'" />
                                <span class="mr-2"></span>
                                <input value="{{ $profile->mobile }}" name="mobile" class="easyui-textbox" style="width:335px;height:22px;" tabindex="11" data-options="label:'<b>*</b>No. Handphone:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->pob }}" name="pob" class="easyui-textbox" style="width:335px;height:22px;" tabindex="3" data-options="label:'<b>*</b>Tempat Lahir:',labelWidth:'125px'" />
                                <span class="mr-2"></span>
                                <input value="{{ $profile->email }}" name="email" class="easyui-textbox" style="width:335px;height:22px;" tabindex="12" data-options="label:'Email:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->dob }}" name="dob" class="easyui-datebox" style="width:250px;height:22px;" tabindex="4" data-options="label:'<b>*</b>Tanggal Lahir:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                                <span style="margin-right:93px;"></span>
                                <input value="{{ $profile->work_start->format('d/m/Y') }}" name="work_start" class="easyui-textbox" style="width:250px;height:22px;" tabindex="13" data-options="label:'Tanggal Kerja:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Jenis Kelamin:</label>
                                @if ($profile->gender == 1)
                                    <input name="gender" class="easyui-radiobutton" value="1" data-options="label:'Laki-Laki',labelPosition:'after'" checked="checked" />
                                    <input name="gender" class="easyui-radiobutton" value="2" data-options="label:'Perempuan',labelPosition:'after'" />
                                @else 
                                    <input name="gender" class="easyui-radiobutton" value="1" data-options="label:'Laki-Laki',labelPosition:'after'" />
                                    <input name="gender" class="easyui-radiobutton" value="2" data-options="label:'Perempuan',labelPosition:'after'" checked="checked" />
                                @endif
                            </div>
                            <div class="mb-1">
                                <input value="{{ $profile->address }}" name="address" class="easyui-textbox" style="width:335px;height:95px;" tabindex="5" data-options="label:'<b>*</b>Alamat:',labelWidth:'125px',multiline:true" />
                                <span class="mr-2"></span>
                                <input value="{{ $profile->remark }}" name="remark" class="easyui-textbox" style="width:335px;height:90px;" tabindex="6" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                        </div>
                        <div class="col-3">
                            <fieldset style="width:148px;margin-top:-7px;">
                                <legend>Foto:</legend>
                                <input name="photo" id="photo-employee-profile" class="easyui-filebox" data-options="prompt:'Gambar',buttonText:'Pilih',accept:'image/*'" style="width:100%">
                                <div class="mt-1 mb-1 img-preview">
                                    <img id="preview-img-employee-profile" src="{{ asset('img/img-preview.png') }}" style="display:block;margin:auto;padding:auto;object-fit:cover;height:125px;width:125px;">
                                </div>
                                <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Delete'" onclick="clearPreview('photo-employee-profile','preview-img-employee-profile')" style="width:125px;">Hapus</a>
                            </fieldset>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div title="Akun Aplikasi" class="pt-3">
                <form id="form-employee-profile-account" method="post">
                <input type="hidden" name="id" value="{{ $user_role->model_id }}">
                <input type="hidden" name="name" value="{{ $profile->name }}" />
                <input type="hidden" name="department_id" value="{{ $user->department_id }}" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input value="{{ $profile->email }}" name="email" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'Akun:',labelWidth:'175px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input value="default-no-change" name="password" type="password" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Kata Sandi:',labelWidth:'175px'" />
                            </div>
                            <div class="mb-1">
                                <input value="default-no-change" name="password_conf" id="userPassConf" type="password" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Konfirmasi Kata Sandi:',labelWidth:'175px'" />
                            </div>
                            <div class="mb-2">
                                <select name="roles" id="EmployeeProfileRole" class="easyui-combobox" style="width:400px;height:22px;" data-options="label:'Grup Pengguna:',labelWidth:'175px',labelPosition:'before',panelHeight:125,readonly:true">
                                    <option value="">---</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <a href="javascript:void(0)" class="easyui-linkbutton small-btn" style="height:22px;width: auto;margin-left: 175px;" onclick="$('#form-employee-profile-account').submit()">Ubah Kata Sandi</a>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div title="Log Aktivitas" class="pt-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <label class="mb-1">Dari Tanggal:</label>
                            <span class="mr-2"></span>
                            <label class="mb-1" style="margin-left:30px;">Sampai Tanggal:</label>
                        </div>
                        <div class="col-12">
                            <form id="EmployeeProfileLogForm">
                            <input value="{{ date('Y-m-d', strtotime('-1 day')) }}" name="from_date" id="EmployeeProfileFromDate" class="easyui-datebox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" />
                            <span class="mr-2"></span>
                            <input value="{{ date('Y-m-d') }}" name="to_date" id="EmployeeProfileToDate" class="easyui-datebox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" />
                            <span class="mr-2"></span>
                            <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Search'" style="height:22px;width: auto;" onclick="$('#tb-employee-profile-log').datagrid('load', { _token: '{{ csrf_token() }}', fstart: $('#EmployeeProfileFromDate').datebox('getValue'), fend: $('#EmployeeProfileToDate').datebox('getValue') })">Cari</a>
                            <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Clear'" style="height:22px;width: auto;" onclick="$('#EmployeeProfileLogForm').form('reset');$('#tb-employee-profile-log').datagrid('load', { _token: '{{ csrf_token() }}', fuser: '{{ auth()->user()->email }}', fstart: $('#EmployeeProfileFromDate').datebox('getValue'), fend: $('#EmployeeProfileToDate').datebox('getValue') })">Batal</a>
                            </form>
                        </div>
                        <div class="col-12 mt-2">
                            <table id="tb-employee-profile-log" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                                data-options="method:'post',url: '{{ url('audit/log/data') }}',queryParams: { _token: '{{ csrf_token() }}', fuser: '{{ auth()->user()->email }}', fstart: $('#EmployeeProfileFromDate').datebox('getValue'), fend: $('#EmployeeProfileToDate').datebox('getValue') },pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                                <thead>
                                    <tr>
                                        <th data-options="field:'user',width:50,hidden:true">Email</th>
                                        <th data-options="field:'created',width:150,resizeable:true,align:'center'">Waktu</th>
                                        <th data-options="field:'ip',width:100,resizeable:true,align:'center'">Alamat IP</th>
                                        <th data-options="field:'remark',width:300,resizeable:true">Aktivitas</th>
                                        <th data-options="field:'before',width:300,resizeable:true">Sebelum</th>
                                        <th data-options="field:'after',width:300,resizeable:true">Sesudah</th>
                                        <th data-options="field:'browser',width:640,resizeable:true">Browser</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var title = document.getElementById("title-employee-profile")
    var mark = document.getElementById("mark-employee-profile")
    $(function () {
        sessionStorage.formProfil_Saya = "init"
        $("#photo-employee-profile").filebox({
            onChange: function(newValue, oldValue) {
                previewFile('photo-employee-profile','preview-img-employee-profile')
            }
        })
        // set value
        $("#EmployeeProfileMarital").combobox("setValue", {{ $profile->marital }})
        $("#EmployeeProfileTribe").combobox("setValue", {{ $profile->tribe }})
        $("#photo-employee-profile").filebox("setText", "{{ $profile->photo }}")
        if ("{{ $profile->photo }}" != "") {
            $("#preview-img-employee-profile").attr("src", "/storage/uploads/employee/" + "{{ $profile->photo }}")
        } else {
            clearPreview("photo-employee-profile","preview-img-employee-profile")
        }
        $("#EmployeeProfileRole").combobox("setValue", {{ $user_role->role_id }})
        $("#form-employee-profile-account").ajaxForm({
            url: "{{ url('user/store') }}",
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#form-region").waitMe({effect:"facebook"})
                if (formData[4].value != formData[5].value) {
                    $('#form-region').waitMe('hide')
                    $.messager.alert("Peringatan", "Kata Sandi dan Konfirmasi Kata Sandi harus sama.", "warning")
                    return false
                }
            },
            success: function(response) {
                $('#form-region').waitMe('hide')
                if (response.success) {
                    $.messager.alert({
                        title: "Informasi",
                        msg: "Anda telah mengubah kata sandi, anda harus keluar sistem dan masuk kembali dengan kata sandi baru.",
                        fn: function() {
                            exitApp("{{ url('logout') }}", "{{ csrf_token() }}")
                        }
                    })
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            },
            error: function(xhr) {
                failResponse(xhr)
                $('#form-region').waitMe('hide')
            }
        })
    })
    function saveEmployeeProfile() {
        $("#form-employee-profile-main").ajaxSubmit({
            url: "{{ url('hr/store') }}",
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#form-region").waitMe({effect:"facebook"})
            },
            success: function(response) {
                if (response.success) {
                    Toast.fire({icon:"success",title:response.message})
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $('#form-region').waitMe('hide')
            },
            error: function(xhr) {
                failResponse(xhr)
                $('#form-region').waitMe('hide')
            }
        })
        return false
    }
</script>