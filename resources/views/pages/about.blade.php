@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
    $PanelHeight = $InnerHeight - 229 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Tentang Aplikasi</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div id="page-manual-app" class="container-fluid">
            <div class="row">
                <div class="col-6 pt-3">
                    <div>
                        <p><b>SIMTIA</b> (Sistem Informasi Ma'had Tahfidz dan Ilmu Al Qur'an), merupakan aplikasi berbasis web yang dibuat untuk mengelola kegiatan dan administrasi di Lembaga Tahfidz Al Qur'an, seperti Pondok Ma'had Tahfidz dan Rumah Qur'an.</p>
                        <h6>Fitur aplikasi</h6>
                        <ul>
                            <li>Modul Data Master, Akademik dan Keuangan yang berguna untuk mengelola aktivitas KBM dan keuangan</li>
                            <li>Hak akses pengguna berdasarkan level grup dan menu aplikasi</li>
                            <li>Fitur pembagian kamar santri yang dibutuhkan untuk mengatur penempatan kamar bagi santri mukim</li>
                            <li>Fitur kartu setoran hafalan santri yang berguna untuk memantau perkembangan hafalan santri beserta status hafalan</li>
                            <li>Fitur Ekspor data dan laporan dalam format Excel, Word dan PDF</li>
                        </ul>
                        <span>Azmi Studio &copy 2022</span><br/>
                        <span>MIT License - Open Source</span><br/>
                        <span><a href="https://github.com/azmistudio/simtia">Kode sumber</a></span>
                    </div>
                </div>
                <div class="col-6 pt-3">
                    <h6>&raquo Versi terpasang : {{ $version }}</h6>
                    <div class="easyui-panel" title="Log perubahan" style="width:100%;height:{{ $PanelHeight }};padding:10px;">
                        {!! $release_desc !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>