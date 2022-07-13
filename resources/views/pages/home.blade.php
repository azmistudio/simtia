@extends($ajax == false ? 'layouts.app' : 'layouts.empty') 
@if ($ajax == false) @section('content') @endif
<div id="main-layout" class="easyui-layout">
    <div data-options="region:'north'" style="background-color: #f8f8f8;height: 32px;overflow: hidden;">
         <div class="row" style="margin: 0 !important;">
            <div class="col-9 p-0">
                <div id="main-menu">
                    <div class="easyui-panel" style="border-color: #dedede;" data-options="border:false">
                        <a style="font-weight:600;" class="easyui-menubutton" data-options="menu:'#mm1',iconCls:'icon-home'">Utama</a>
                        <a style="font-weight:600;" class="easyui-menubutton" data-options="menu:'#mm2',iconCls:'icon-database'">Data Master</a>
                        <a style="font-weight:600;" class="easyui-menubutton" data-options="menu:'#mm3',iconCls:'icon-academic'">Akademik</a>
                        <a style="font-weight:600;" class="easyui-menubutton" data-options="menu:'#mm4',iconCls:'icon-money'">Keuangan</a>
                        <a style="font-weight:600;" class="easyui-menubutton" data-options="menu:'#mm5',iconCls:'icon-report'">Laporan</a>
                    </div>
                    <div id="mm1" style="width:200px;">
                        <div onclick="openTabMenu('Grup Pengguna','group','init')">Grup Pengguna</div>
                        <div onclick="openTabMenu('Pengguna','user','init')">Pengguna</div>
                        <div onclick="openTabMenu('Profil Saya','hr/profile')">Profil Saya</div>
                        <div class="menu-sep"></div>
                        <div onclick="openTabMenu('Log Aplikasi','audit/log')">Log Aplikasi</div>
                        <div onclick="openTabMenu('Manual Aplikasi','home/manual')">Manual Aplikasi</div>
                        <div onclick="openTabMenu('Tentang Aplikasi','home/about')">Tentang Aplikasi</div>
                        <div class="menu-sep"></div>
                        <div onclick="exitApp('{{ url('logout') }}', '{{ csrf_token() }}')">Keluar Aplikasi</div>
                    </div>
                    <div id="mm2" style="width:225px;">
                        <div onclick="openTabMenu('SDM','hr','init')">Sumber Daya Manusia</div>
                        <div onclick="openTabMenu('Departemen','department','init')">Departemen</div>
                        <div onclick="openTabMenu('Lembaga','institute','init')">Profil Lembaga</div>
                        <div onclick="openTabMenu('Kamar Santri','general/room/student','init')">Kamar Santri</div>
                        <div class="menu-sep"></div>
                        <div data-options="iconCls:'icon-submenu'"><b>Referensi</b></div>
                        <div onclick="openTabMenu('Ref Sistem','reference','init')">Referensi Sistem</div>
                        <div onclick="openTabMenu('Ref Akademik','academic','init')">Referensi Akademik</div>
                        <div data-options="iconCls:'icon-submenu'"><b>Referensi Akuntansi</b></div>
                        <div onclick="openTabMenu('Tahun Buku','finance/book/year','init')">Tahun Buku</div>
                        <div onclick="openTabMenu('Kode COA','finance/coa', 'init')">Kode Akun Perkiraan (COA)</div>
                        <div onclick="openTabMenu('Tutup Buku','finance/book/close','init')">Tutup Buku</div>
                    </div>
                    <div id="mm3" style="width:250px;">
                        <div>
                            <span>Penerimaan Santri Baru</span>
                            <div>
                                <div onclick="openTabMenu('PSB Proses','academic/admission','init')">Proses Penerimaan</div>
                                <div onclick="openTabMenu('PSB Kelompok','academic/admission/prospective-group','init')">Kelompok Calon Santri</div>
                                <div onclick="openTabMenu('PSB Calon','academic/admission/prospective-student','init')">Pendataan Calon Santri</div>
                                <div onclick="openTabMenu('PSB Penempatan','academic/admission/placement','init')">Penempatan Santri Baru</div>
                                <div class="menu-sep"></div>
                                <div onclick="openTabMenu('PSB Konfigurasi','academic/admission/config','init')">Konfigurasi</div>
                                <div onclick="openTabMenu('Tambahan Kolom','academic/admission/column','init')">Tambahan Kolom Data Santri</div>
                            </div>
                        </div>
                        <div onclick="openTabMenu('Pembagian Kamar','academic/room/placement','init')">Pembagian Kamar Santri</div>
                        <div>
                            <span>Pelajaran</span>
                            <div>
                                <div onclick="openTabMenu('Ref Pelajaran','academic/lesson/reference','init')">Referensi Pelajaran</div>
                                <div onclick="openTabMenu('Data Pelajaran','academic/lesson','init')">Data Pelajaran</div>
                                <div onclick="openTabMenu('RPP','academic/lesson/plan','init')">Rencana Program Pembelajaran</div>
                                <div onclick="openTabMenu('Jenis Pengujian','academic/lesson/exam','init')">Jenis Pengujian</div>
                            </div>
                        </div>
                        <div>
                            <span>Data Guru</span>
                            <div>
                                <div onclick="openTabMenu('Guru','academic/teacher','init')">Guru Pelajaran</div>
                                <div class="menu-sep"></div>
                                <div onclick="openTabMenu('Aturan Grading','academic/lesson/grading','init')">Aturan Grading Rapor Santri</div>
                                <div onclick="openTabMenu('Aturan Penilaian','academic/lesson/assessment','init')">Aturan Nilai Rapor Santri</div>
                            </div>
                        </div>
                        <div class="menu-sep"></div>
                        <div>
                            <span>Jadwal Belajar</span>
                            <div>
                                <div onclick="openTabMenu('Jam Belajar','academic/lesson/schedule/time','init')">Jam Belajar</div>
                                <div onclick="openTabMenu('Jadwal Guru','academic/lesson/schedule/teaching','init')">Jadwal Guru & Kelas</div>
                                <div onclick="openTabMenu('Rekapitulasi Jadwal','academic/lesson/schedule/recap','init')">Rekapitulasi Jadwal Guru</div>
                            </div>
                        </div>
                        <div onclick="openTabMenu('Kalender Akademik','academic/calendar','init')">Kalender Akademik</div>
                        <div class="menu-sep"></div>
                        <div onclick="openTabMenu('Pendataan Santri','academic/student','init')">Data Santri</div>
                        <div>
                            <span>Presensi</span>
                            <div>
                                <div onclick="openTabMenu('Presensi Harian','academic/presence/daily','init')">Presensi Harian</div>
                                <div onclick="openTabMenu('Presensi Pelajaran','academic/presence/lesson','init')">Presensi Pelajaran</div>
                            </div>
                        </div>
                        <div>
                            <span>Penilaian</span>
                            <div>
                                <div onclick="openTabMenu('Penilaian Pelajaran','academic/assessment/lesson', 'init')">Penilaian Pelajaran</div>
                                <div onclick="openTabMenu('Perhitungan Rapor','academic/assessment/report/formula', 'init')">Perhitungan Nilai Rapor</div>
                                <div onclick="openTabMenu('Komentar Rapor','academic/assessment/report/comment', 'init')">Komentar Rapor</div>
                                <div onclick="openTabMenu('Rapor Santri','academic/assessment/report/score', 'init')">Nilai Rapor Santri</div>
                                <div class="menu-sep"></div>
                                <div onclick="openTabMenu('Audit Nilai','academic/assessment/score/audit', 'init')">Audit Perubahan Nilai</div>
                            </div>
                        </div>
                        <div onclick="openTabMenu('Kartu Setoran','academic/student/memorize-card','init')">Kartu Setoran Hafalan Santri</div>
                        <div class="menu-sep"></div>
                        <div>
                            <span>Kenaikan & Kelulusan</span>
                            <div>
                                <div onclick="openTabMenu('Kenaikan Kelas','academic/graduation/promote', 'init')">Kenaikan Kelas</div>
                                <div onclick="openTabMenu('Tidak Naik','academic/graduation/unpromote', 'init')">Tidak Naik Kelas</div>
                                <div onclick="openTabMenu('Kelulusan Pindah','academic/graduation/mutation', 'init')">Kelulusan - Pindah Departemen</div>
                                <div onclick="openTabMenu('Kelulusan Alumni','academic/graduation/alumni', 'init')">Kelulusan - Alumni</div>
                            </div>
                        </div>
                        <div onclick="openTabMenu('Mutasi Santri','academic/student/mutation','init')">Mutasi Santri</div>
                    </div>
                    <div id="mm4" style="width:250px;">
                        <div data-options="iconCls:'icon-submenu'"><b>Penerimaan</b></div>
                        <div onclick="openTabMenu('Jenis Penerimaan','finance/receipt/type', 'init')">Jenis Penerimaan</div>
                        <div onclick="openTabMenu('Besar Pembayaran','finance/receipt/payment/major', 'init')">Besar Pembayaran</div>
                        <div onclick="openTabMenu('Transaksi Penerimaan','finance/receipt', 'init')">Transaksi Penerimaan</div>
                        <div data-options="iconCls:'icon-submenu'"><b>Pengeluaran</b></div>
                        <div onclick="openTabMenu('Transaksi Pengeluaran','finance/expenditure', 'init')">Transaksi Pengeluaran</div>
                        <div class="menu-sep"></div>
                        <div data-options="iconCls:'icon-submenu'"><b>Tabungan Santri</b></div>
                        <div onclick="openTabMenu('Jenis TabSantri','finance/saving/student/type', 'init')">Jenis Tabungan Santri</div>
                        <div onclick="openTabMenu('Tabungan Santri','finance/saving/student', 'init')">Transaksi Tabungan Santri</div>
                        <div data-options="iconCls:'icon-submenu'"><b>Tabungan Pegawai</b></div>
                        <div onclick="openTabMenu('Jenis TabPegawai','finance/saving/employee/type', 'init')">Jenis Tabungan Pegawai</div>
                        <div onclick="openTabMenu('Tabungan Pegawai','finance/saving/employee', 'init')">Transaksi Tabungan Pegawai</div>
                        <div class="menu-sep"></div>
                        <div onclick="openTabMenu('Jurnal Umum','finance/journal', 'init')">Jurnal Umum</div>
                    </div>
                    <div id="mm5" style="width:200px;">
                        <div onclick="openTabMenu('Laporan Akademik','academic/report','init')">Laporan Akademik</div>
                        <div onclick="openTabMenu('Laporan Keuangan','finance/report','init')">Laporan Keuangan</div>
                    </div>
                </div>
            </div>
            <div class="col-3 p-0">
                <div id="user-menu">
                    <div class="easyui-panel" style="border-color: #dedede;text-align: right;" data-options="border:false">
                        <a href="#"><span id="ringer" class="ms-Icon {{ $notification == 0 ? 'ms-Icon--Ringer' : 'ms-Icon--RingerActive' }}" style="position:relative;top:3px;"></span></a>
                        <a href="#" class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Calendar'">{{ $dateNow }}</a>
                        <a href="#" class="easyui-linkbutton" data-options="plain:true"><span id="hijri-date"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="row mb-1" style="background-color: beige;">
            <div class="col-md-6">
                <p class="mb-0" style="padding: 4px 7px;font-weight: 600;">DEPARTEMEN: 
                    @if ($total_dept > 0 && $department->is_all == 1)
                    <span>{{ $department->name }} (SEMUA DEPARTEMEN)</span>
                    @else
                    <span>{{ $department->name }}</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6 text-right">
                <p class="mb-0" style="padding: 4px 7px;">
                    <span style="font-weight:600">SEMESTER {{ Session::get('semester') }}</span> / <span style="font-weight:600">TAHUN AJARAN {{ Session::get('schoolyear') }}</span>
                </p>
            </div>
        </div>
        <div id="tt" class="easyui-tabs" fit="true" border="false" plain="true">
            <div title="Pintasan" class="content-doc">
                <div class="container-fluid">
                    <div class="row"><div class="col mt-2 mb-2"><span class="title-shortcut">Akademik</span></div></div>
                    <div class="row align-items-center" style="padding: 5px 0;">
                        <div class="col-md-auto"><a onclick="openTabMenu('Presensi Harian','academic/presence/daily','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-document32',size:'large',iconAlign:'top'">Presensi<br/>Harian</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Presensi Pelajaran','academic/presence/lesson','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-document32',size:'large',iconAlign:'top'">Presensi<br/>Pelajaran</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Pendataan Santri','academic/student','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-users32',size:'large',iconAlign:'top'">Data<br/>Santri</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Guru','academic/teacher','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-sales32',size:'large',iconAlign:'top'">Data<br/>Guru</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Data Pelajaran','academic/lesson','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-document32',size:'large',iconAlign:'top'">Data<br/>Pelajaran</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Kalender Akademik','academic/calendar','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-calendar32',size:'large',iconAlign:'top'">Kalender<br/>Akademik</a></div>
                    </div>
                    <div class="row"><div class="col mt-2 mb-2"><span class="title-shortcut">Kas dan Keuangan</span></div></div>
                    <div class="row align-items-center" style="padding: 5px 0;">
                        <div class="col-md-auto"><a onclick="openTabMenu('Kode COA','finance/coa', 'init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-coa32',size:'large',iconAlign:'top'">Daftar<br />Akun (COA)</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Jurnal Umum','finance/journal', 'init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-genledger32',size:'large',iconAlign:'top'">Jurnal<br />Umum</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Transaksi Penerimaan','finance/receipt','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-income32',size:'large',iconAlign:'top'">Transaksi<br />Penerimaan</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-left" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Transaksi Pengeluaran','finance/expenditure','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-paybank32',size:'large',iconAlign:'top'">Transaksi<br />Pengeluaran</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                        <div class="col-md-auto"><a onclick="openTabMenu('Tabungan Santri','finance/saving/student','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-reconcile32',size:'large',iconAlign:'top'">Tabungan<br />Santri</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>                       
                        <div class="col-md-auto"><a onclick="openTabMenu('Tabungan Pegawai','finance/saving/employee','init')" style="width:120px;padding:6px;" class="easyui-linkbutton" data-options="iconCls:'icon-reconcile32',size:'large',iconAlign:'top'">Tabungan<br />Pegawai</a></div>
                        <div class="col-md-auto"><i class="gg-arrow-long-right" style="visibility:hidden"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div data-options="region:'south',split:false,collapsible:false" style="height:25px;overflow: hidden;">
        <div id="bottom-bar" class="row" style="margin: 0 !important;background-color: #f5f6f7;">
            <div class="col-3" style="padding:0;">
                <p style="padding:5px 10px;font-size: 12px;">Masuk sebagai: <b>{{ auth()->user()->name }}</b></p>
            </div>
            <div class="col-6" style="padding:0;">
                <p style="padding:5px 10px;font-size: 12px;text-align:center;">Periode Akuntansi: <span id="accounting-month"><b>{{ date('m/Y') }}</b></span> - Dari <span id="accounting-start"><b>{{ date('01 M Y') }}</b></span> s.d <span id="accounting-end"><b>{{ date('t M Y') }}</b></span></p>
            </div>
            <div class="col-3" style="padding:0;text-align:right;">
                <p style="padding:5px 10px;font-size: 12px;">Waktu: <b><span id="clock"></span></b></p>
            </div>
        </div>
    </div>
</div>
<!-- dialogs -->
<div id="dd-tribe" class="easyui-dialog p-2" title="Data Referensi - Suku" style="width:330px;" data-options="iconCls:'ms-Icon ms-Icon--Database',resizable:true,modal:true,closed:true,buttons:'#bb-tribe'">
    <form id="fff-tribe" method="post">
    @csrf
    <input type="hidden" name="code" />
    <input type="hidden" name="order" />
    <input type="hidden" name="parent" />
    <input type="hidden" name="isNewRecord" value="true" />
    <input type="hidden" name="category" value="hr_tribe" />
    <div class="mb-1">
        <input name="name" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Nama Suku:',labelWidth:'125px'">
    </div>
    <div class="mb-1">
        <input name="remark" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Keterangan:',labelWidth:'125px'">
    </div>
    </form>
</div>
<div id="bb-tribe">
	<a class="easyui-linkbutton small-btn filter-box" onclick="saveTribe()">Simpan</a>
	<a class="easyui-linkbutton small-btn filter-box ml-0" onclick="$('#fff-tribe').form('reset');$('#dd-tribe').dialog('close')">Batal</a>
</div>
{{-- receipt --}}
<div id="receipt-w" class="easyui-window" style="width:900px;height:400px" data-options="modal:true,closed:true,minimizable:false"></div>
{{-- tab contextmenu --}}
<div id="tab-ctxmenu" class="easyui-menu" style="width:120px;">
    <div onclick="closeOtherTab()">Tutup Tab Lain</div>
    <div onclick="closeAllTab()">Tutup Semua Tab</div>
</div>
<script type="text/javascript">
    let selectedCtxMenu = 0
    $(function () {
        setInterval(startTime, 100)
        $(window).bind('beforeunload', function(event) {
            return "Data yang belum tersimpan akan hilang jika Peramban ditutup."
        })
        $.get("{{ url('hijri') }}", $.param({ _token: '{{ csrf_token() }}' }, true), function(response) {
            if (response.success) {
                $("#hijri-date").text("|" + "\u00a0" + "\u00a0" + "\u00a0" + response.message)
            }
        }).fail(function (data, textStatus, xhr) {
            if (data.status == 500) {
                $("#hijri-date").text("")
            }
        })
        $('#tt').tabs({
            onContextMenu: function (e, title, index) {
                e.preventDefault()
                $("#tab-ctxmenu").menu("show", {
                    left: e.pageX,
                    top: e.pageY
                })
                selectedCtxMenu = index
            }
        })
        $('#ringer').tooltip({
            content: $('<div></div>'),
            showEvent: 'click',
            onUpdate: function(content){
                content.panel({
                    width: 250,
                    height: 150,
                    border: true,
                    title: 'Notifikasi',
                    href: "{{ url('notifications') }}",
                    onLoad: function(){
                        $('#ringer').removeClass("ms-Icon--RingerActive").addClass("ms-Icon--Ringer")
                    }
                })
            },
            onShow: function(){
                var t = $(this)
                t.tooltip('tip').unbind().bind('mouseenter', function(){
                    t.tooltip('show')
                }).bind('mouseleave', function(){
                    t.tooltip('hide')
                })
            }
        })
    })
    function openTabMenu(name, href, type) {
        if ($('#tt').tabs('exists', name)) {
            $('#tt').tabs('select', name);
        } else {
            var innerHeight = window.innerHeight
            var innerWidth = window.innerWidth
            $('#tt').tabs('add', {
                title: name,
                href: href+'?w='+innerHeight+'.'+innerWidth+'&t='+type,
                closable: true,
                tools:[{
                    iconCls:'icon-mini-refresh',
                    handler:function(){
                        $('#tt').tabs("getSelected").panel("refresh", href+'?w='+innerHeight+'.'+innerWidth+'&t='+type)
                    }
                }]
            });
        }
    }
    function openTab(plugin) {
        if ($('#tt').tabs('exists', plugin.text)) {
            $('#tt').tabs('select', plugin.text)
        } else {
            var innerHeight = window.innerHeight
            $('#tt').tabs('add', {
                title: plugin.text,
                href: plugin.href+'?w='+innerHeight+'.'+innerWidth+'&t=init',
                closable: true,
            })
        }
    }
    function closeOtherTab() {
        var tabs = $("#tt").tabs("tabs")
        for (var i = tabs.length - 1; i > 0; i--) {
            if (i != 0 && i != selectedCtxMenu) {
                $("#tt").tabs("close", i)
            }
        }
    }
    function closeAllTab() {
        var tabs = $("#tt").tabs("tabs")
        for (var i = tabs.length - 1; i > 0; i--) {
            $("#tt").tabs("close", i)
        }
        $("#tt").tabs("close", 1)
    }
    function startTime() {
        var today = new Date(), curr_hour=today.getHours(), curr_min=today.getMinutes(), curr_sec=today.getSeconds();
        curr_hour = checkTime(curr_hour);
        curr_min  = checkTime(curr_min);
        curr_sec  = checkTime(curr_sec);
        document.getElementById('clock').innerHTML=curr_hour+":"+curr_min+":"+curr_sec;
    }
    function checkTime(i) {
        if (i<10) { i="0" + i; }
        return i;
    }
    function tribeDialog() {
        $("#fff-tribe").form("reset")
        $("#dd-tribe").dialog("open")
    }
    function saveTribe(param) {
        $("#fff-tribe").ajaxSubmit({
            url: "{{ url('reference/store/-1') }}",
            success: function(response) {
                if (response.success) {
                    $.messager.alert('Informasi', response.message)
                    $('#dd-tribe').dialog('close')
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            },
            error: function(xhr) {
                failResponse(xhr)
            }
        })
        return false
    }
    function exitApp(route, token) {
        $.post(route, $.param({ _token: token }, true), function(response) {
            if (response.success) {
                window.location.href = "/"
            } else {
                $.messager.alert('Peringatan', 'Terjadi gangguan, silahkan ulangi', 'error')
            }
        })
        return false
    }
</script>
@if ($ajax == false) @endsection @endif