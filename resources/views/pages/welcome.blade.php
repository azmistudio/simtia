@extends($ajax == false ? 'layouts.app' : 'layouts.empty') 
@if ($ajax == false) @section('content') @endif
<a class="github-fork-ribbon" href="https://github.com/azmistudio/simtia" data-ribbon="Fork me on GitHub" title="Fork me on GitHub">Fork me on GitHub</a> 
<header class="app-bar" data-role="appbar" style="background-color:#2b579a;height: 222px;padding-top: 20px;align-items: flex-start;">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-8">
                <span class="fg-white">Sistem Informasi <br/>Ma'had Tahfidz dan Ilmu Al-Qur'an</span>
            </div>
            <div class="col-4 text-right">
                <a class="fg-white">SIMTIA<br/>v{{ config('app.version', '1.0') }}</a>
            </div>
        </div>
    </div>
</header>
<div class="page-content" style="top: -120px;position: relative;z-index: 1032;">
    <div class="container">
        <div class="row justify-content-md-center mt-2">
            <div class="col-6">
                <div id="login-app" class="easyui-panel" title="Input Kredensial" style="width:100%;padding:20px 50px;" data-options="iconCls:'icon-login'">
                    <div class="row">
                        <div class="col-12 mb-2 text-center">
                            <img class="avatar" src="{{ Session::get('institute_logo') }}" style="margin-top:-5px;width: 50px;">&nbsp;&nbsp;
                            <span class="label" style="font-size:18px;"><b>{{ strtoupper(Session::get('institute')) }}</b></span><br/>
                        </div>
                        <div class="col-12 mb-3"><hr/></div>
                    </div>
                    <form id="login-form" method="post">
                        <div class="mb-1 row">
                            <label for="username" class="col-sm-4 col-form-label" style="line-height:1 !important;">*Email: </label>
                            <div class="col-sm-8">
                                <input name="email" type="email" tabindex="1" class="easyui-textbox" style="width:100%" data-options="required:true,iconCls:'icon-man',iconWidth:38" />
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputPassword" class="col-sm-4 col-form-label" style="line-height:1 !important;">*Kata Sandi: </label>
                            <div class="col-sm-8">
                                <input name="password" type="password" tabindex="2" class="easyui-textbox" style="width:100%" data-options="required:true,iconCls:'icon-lock',iconWidth:38" />
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-md-4"></label>
                            <div class="col-md-8">
                                <button type="button" class="easyui-linkbutton" tabindex="3" onclick="logMeIn({{ $is_mobile }})">Masuk Aplikasi</button>
                                <button type="button" class="easyui-linkbutton" tabindex="4" onclick="$('#login-form').form('clear')">Batal</button>
                            </div>
                        </div>
                    </form>
                    <br/>
                    @if ($is_mobile)
                    <div class="row">
                        <div class="col-12">
                            <div class="well-warning text-center">
                                <i>Mohon maaf, tampilan aplikasi tidak dirancang untuk responsif (akses via Handphone / Tablet), silahkan akses melalui PC/Laptop.</i>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12 mb-1"><hr/></div>
                        <div class="col-12 text-center">
                            <span style="font-size:12px;">Dikembangkan oleh <a href="https://github.com/azmistudio/simtia" target="_blank">Azmi Studio</a> &copy 2021 - {{ date('Y') }}</span><br/>
                        </div>
                    </div>
                </div>
                <div class="well mt-2">
                    <h6>Akun Akses Demo (<i><small>Data akan direset setiap pekan</small></i>)</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Sandi</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>admin@simtia.org</td>
                                <td>123456</td>
                                <td>Administrator</td>
                            </tr>
                            <tr>
                                <td>kepsek@simtia.org</td>
                                <td>123456</td>
                                <td>Kepala Sekolah</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function logMeIn(is_mobile) {
        if (is_mobile) {
            $.messager.confirm('Peringatan', 'Jika diakses via Handphone/Tablet, tampilan aplikasi tidak nyaman dilihat, tetap lanjutkan?', function(r){
                if (r) {
                    submitLogin("{{ url('login') }}", "{{ csrf_token() }}")
                }
            })
        } else {
            submitLogin("{{ url('login') }}", "{{ csrf_token() }}")
        }
    }
</script>
@if ($ajax == false) @endsection @endif