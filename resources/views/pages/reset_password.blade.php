@extends($ajax == false ? 'layouts.app' : 'layouts.empty') 
@if ($ajax == false) @section('content') @endif
<header class="app-bar" data-role="appbar" style="background-color:#2b579a;height: 222px;padding-top: 20px;align-items: flex-start;">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-8">
                <span class="fg-white">Sistem Informasi Manajemen Akademik dan Keuangan<br/>Lembaga Tahfidz Al-Qur'an</span>
            </div>
            <div class="col-4 text-right">
                <a class="fg-white">SIMAK LTA<br/>v{{ config('app.version', '1.0') }}</a>
            </div>
        </div>
    </div>
</header>
<div class="page-content" style="top: -120px;position: relative;z-index: 1032;">
    <div class="container">
        <div class="row justify-content-md-center mt-2">
            <div id="login-app" class="col-6">
                <div class="easyui-panel" title="Atur Ulang Kata Sandi" style="width:100%;padding:30px 60px;" data-options="iconCls:'ms-Icon ms-Icon--Reset'">
                    <div class="row">
                        <div class="col-12 mb-2 text-center">
                            <img class="avatar" src="{{ Session::get('institute_logo') }}" style="margin-top:-5px;width: 50px;">&nbsp;&nbsp;
                            <span class="label" style="font-size:18px;"><b>{{ Session::get('institute') }}</b></span>
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
                        <div class="mb-1 row">
                            <label for="inputPassword" class="col-sm-4 col-form-label" style="line-height:1 !important;">*Kata Sandi: </label>
                            <div class="col-sm-8">
                                <input name="password" type="password" tabindex="2" class="easyui-textbox" style="width:100%" data-options="required:true,iconCls:'icon-lock',iconWidth:38" />
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="inputPassword" class="col-sm-4 col-form-label" style="line-height:1 !important;">*Konfirmasi Sandi: </label>
                            <div class="col-sm-8">
                                <input name="password" type="password" tabindex="3" class="easyui-textbox" style="width:100%" data-options="required:true,iconCls:'icon-lock',iconWidth:38" />
                            </div>
                        </div>
                        <div style="padding:5px 0;margin-left:149px;">
                            <button type="button" class="easyui-linkbutton" tabindex="4" onclick="resetPassword('{{ url('login') }}', '{{ csrf_token() }}')" style="width:164px">Atur Ulang</button>
                            <a href="{{ url('/') }}" class="easyui-linkbutton" tabindex="5" style="width:100px">Kembali</a>
                        </div>
                    </form>
                    <br/>
                    <div class="row">
                        <div class="col-12 text-center">
                            <span class="label" style="font-size:15px;"><b>{{ Session::get('foundation') }}</b></span><br/><br/>
                            <span style="font-size:12px;"><a href="https://github.com/azmistudio/simak-lta" target="_blank">Azmi Studio</a> &copy 2021</span><br/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if ($ajax == false) @endsection @endif