@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Laporan Akademik</h5>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:275px">
        <div class="p-1">
            <ul class="easyui-tree">
                <li><a href="javascript:void(0)" onclick="reportPage('academic/report/admission/prospect', 'body-academic-report', 'Data Calon Santri')">Data Calon Santri</a></li>
                <li><a href="javascript:void(0)" onclick="reportPage('academic/report/admission/stat', 'body-academic-report', 'Statistik Penerimaan Santri')">Statistik Penerimaan Santri</a></li>
                <li><a href="javascript:void(0)" onclick="reportPage('academic/report/student/stat', 'body-academic-report', 'Statistik Kesantrian')">Statistik Kesantrian</a></li>
                <li>
                    <span>Presensi Harian</span>
                    <ul>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/daily', 'body-academic-report', 'Presensi Harian Santri')">Presensi Harian Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/daily/class', 'body-academic-report', 'Presensi Harian per Kelas')">Presensi Harian per Kelas</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/daily/absent', 'body-academic-report', 'Data Santri tidak Hadir')">Data Santri Tidak Hadir</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/stat', 'body-academic-report', 'Statistik Kehadiran Santri')">Statistik Kehadiran Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/stat/class', 'body-academic-report', 'Statistik Kehadiran per Kelas')">Statistik Kehadiran per Kelas</a></li>
                    </ul>
                </li>
                <li>
                    <span>Presensi Pelajaran</span>
                    <ul>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson', 'body-academic-report', 'Presensi Pelajaran Santri')">Presensi Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson/class', 'body-academic-report', 'Presensi Pelajaran Santri per Kelas')">Presensi Santri per Kelas</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson/teacher', 'body-academic-report', 'Presensi Pengajar')">Presensi Pengajar</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson/absent', 'body-academic-report', 'Data Santri Tidak Hadir')">Data Santri Tidak Hadir</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson/reflection', 'body-academic-report', 'Refleksi Pengajar')">Refleksi Pengajar</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson/stat', 'body-academic-report', 'Statistik Kehadiran Santri')">Statistik Kehadiran Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/presence/lesson/stat/class', 'body-academic-report', 'Statistik Kehadiran per Kelas')">Statistik Kehadiran per Kelas</a></li>
                    </ul>
                </li>
                <li data-options="state:'closed'">
                    <span>Penilaian</span>
                    <ul>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/plan/average/class', 'body-academic-report', 'Rata-Rata RPP Kelas')">Rata-Rata RPP Kelas</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/plan/average/student', 'body-academic-report', 'Rata-Rata RPP Santri')">Rata-Rata RPP Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/score', 'body-academic-report', 'Nilai Santri')">Nilai Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/score/average', 'body-academic-report', 'Rata-Rata Nilai Santri')">Rata-Rata Nilai Santri</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/score/legger', 'body-academic-report', 'Legger Nilai')">Legger Nilai</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/score/legger/lesson', 'body-academic-report', 'Legger Nilai Rapor Pelajaran')">Legger Nilai Rapor Pelajaran</a></li>
                        <li><a href="javascript:void(0)" onclick="reportPage('academic/report/assessment/score/legger/class', 'body-academic-report', 'Legger Nilai Rapor Kelas')">Legger Nilai Rapor Kelas</a></li>
                    </ul>
                </li>
                <li><a href="javascript:void(0)" onclick="reportPage('academic/report/student/mutation/stat', 'body-academic-report', 'Statistik Mutasi Santri')">Statistik Mutasi Santri</a></li>
            </ul>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="title">
            <h6><span id="title-academic-report">-</span></h6>
        </div>
        <div id="body-academic-report" class="p-1"><div id="loader-academic-report" class="panel-loading" style="visibility:hidden;">Memuat ...</div></div>
    </div>
</div>