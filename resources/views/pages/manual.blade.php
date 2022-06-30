@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Petunjuk Penggunaan Aplikasi</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div id="page-manual-app" class="container-fluid">
            <div class="row">
                <div class="col-12 pt-3">
                    <div>
                        &nbsp;<span>Saat ini tutorial penggunaan aplikasi SIMTIA hanya berupa video, dapat diakses di alamat berikut: <a href="https://www.youtube.com/channel/UCdr9YsgZ_RHNcqkZW0yqR5w/videos" target="_blank">Video tutorial</a></span><br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>