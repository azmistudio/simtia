@php
    $WindowHeight = $requests['window_h'] - 139 . "px";
    $present = array();
    $sick = array();
    $permit = array();
    $absent = array();
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-3 p-2">
            <div class="mb-1">
                <input value="{{ $requests['student_no'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'NIS:',labelWidth:'110px',readonly:true" />
            </div>
            <div class="mb-3">
                <input value="{{ $requests['student_name'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Nama Santri:',labelWidth:'110px',readonly:true" />
            </div>
        </div>
        <div class="col-3 p-2">
            <div class="mb-1">
                <input value="{{ $requests['department'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Departemen:',labelWidth:'110px',readonly:true" />
            </div>
            <div class="mb-3">
                <input value="{{ $requests['grade'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Tingkat:',labelWidth:'110px',readonly:true" />
            </div>
        </div>
        <div class="col-3 p-2">
            <div class="mb-1">
                <input value="{{ $requests['schoolyear'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Tahun ajaran:',labelWidth:'110px',readonly:true" />
            </div>
            <div class="mb-3">
                <input value="{{ $requests['semester'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Semester:',labelWidth:'110px',readonly:true" />
            </div>
        </div>
        <div class="col-3 p-2">
            <div class="mb-1">
                <input value="{{ $requests['class'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Kelas:',labelWidth:'110px',readonly:true" />
            </div>
            <div class="mb-3">
                <input value="{{ $requests['period_start'] }} s.d {{ $requests['period_end'] }}" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Periode:',labelWidth:'110px',readonly:true" />
            </div>
        </div>
    </div>
</div>
<div class="container-fluid" style="height:{{ $WindowHeight }};overflow:auto;">
    <div class="row">
        @foreach ($socials as $social)
        <div class="col-6 p-2">
            <fieldset style="min-height:143px;">
                <legend><b>Komentar Sikap {{ ucfirst($social->aspect) }}</b></legend>
                <table class="table table-bordered table-sm mb-0">
                    <tbody>
                        <tr>
                            <td width="30%" style="vertical-align:top !important;">Predikat: <b>{{ $social->getType->name }}</b></td>
                            <td style="height: 100px;vertical-align:top !important;">{!! html_entity_decode($social->comment) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        @endforeach
        <div class="col-6 p-2">
            <fieldset style="min-height:143px;">
                <legend><b>Nilai Pelajaran</b></legend>
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center">No.</th>
                            <th rowspan="2" class="text-center">Pelajaran</th>
                            @php $i = 0; $columns = array(); @endphp
                            @foreach ($aspects as $aspect)
                                @php $columns[$i++] = array($aspect->id, $i); @endphp
                                <th colspan="2" class="text-center">{{ ucwords($aspect->remark) }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($columns as $column)
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Predikat</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>{!! $tbody_lesson_score !!}</tbody>
                </table>
            </fieldset>
        </div>
        <div class="col-6 p-2">
            <fieldset style="min-height:143px;">
                <legend><b>Deskripsi Nilai Pelajaran</b></legend>
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Pelajaran</th>
                            <th class="text-center">Aspek</th>
                            <th class="text-center">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>{!! $tbody_lesson_score_desc !!}</tbody>
                </table>
            </fieldset>
        </div>
        @if ($requests['daily'] == "true")
        <div class="col-6 p-2">
            <fieldset style="min-height:143px;">
                <legend><b>Presensi Harian</b></legend>
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Hadir</th>
                            <th colspan="2" class="text-center">Sakit</th>
                            <th colspan="2" class="text-center">Ijin</th>
                            <th colspan="2" class="text-center">Alpa</th>
                            <th colspan="2" class="text-center">Cuti</th>
                        </tr>
                        <tr>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{ $presences_daily->present }}</td>
                            <td class="text-center">@if ($presences_daily->present != 0 && $presences_daily->total != 0) {{ round(($presences_daily->present / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->sick }}</td>
                            <td class="text-center">@if ($presences_daily->sick != 0 && $presences_daily->total != 0) {{ round(($presences_daily->sick / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->permit }}</td>
                            <td class="text-center">@if ($presences_daily->permit != 0 && $presences_daily->total != 0) {{ round(($presences_daily->permit / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->absent }}</td>
                            <td class="text-center">@if ($presences_daily->absent != 0 && $presences_daily->total != 0) {{ round(($presences_daily->absent / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->leave }}</td>
                            <td class="text-center">@if ($presences_daily->leave != 0 && $presences_daily->total != 0) {{ round(($presences_daily->leave / $presences_daily->total) * 100,2) }} % @endif</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        @endif
        @if ($requests['lesson'] == "true")
        <div class="col-6 p-2">
            <fieldset style="min-height:143px;">
                <legend><b>Presensi Pelajaran</b></legend>
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center">Pelajaran</th>
                            <th colspan="2" class="text-center">Hadir</th>
                            <th colspan="2" class="text-center">Sakit</th>
                            <th colspan="2" class="text-center">Ijin</th>
                            <th colspan="2" class="text-center">Alpa</th>
                        </tr>
                        <tr>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lessons as $lesson)
                        <tr>
                            <td>{{ strtoupper($lesson->lesson) }}</td>
                            @php $counter = 1; @endphp
                            @foreach ($presences_lesson as $presence)
                                @if ($presence->lesson_id == $lesson->lesson_id)
                                    @php $present[$counter] = $presence->present; @endphp
                                    @php $sick[$counter] = $presence->sick; @endphp
                                    @php $permit[$counter] = $presence->permit; @endphp
                                    @php $absent[$counter] = $presence->absent; @endphp
                                    <td class="text-center">{{ $presence->present }}</td>
                                    <td class="text-center">@if ($presence->present != 0 && $presence->total != 0) {{ round(($presence->present / $presence->total) * 100,2) }} % @endif</td>
                                    <td class="text-center">{{ $presence->sick }}</td>
                                    <td class="text-center">@if ($presence->sick != 0 && $presence->total != 0) {{ round(($presence->sick / $presence->total) * 100,2) }} % @endif</td>
                                    <td class="text-center">{{ $presence->permit }}</td>
                                    <td class="text-center">@if ($presence->permit != 0 && $presence->total != 0) {{ round(($presence->permit / $presence->total) * 100,2) }} % @endif</td>
                                    <td class="text-center">{{ $presence->absent }}</td>
                                    <td class="text-center">@if ($presence->absent != 0 && $presence->total != 0) {{ round(($presence->absent / $presence->total) * 100,2) }} % @endif</td>
                                @endif
                            @php $counter++; @endphp
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                    @php
                        $prs = 0;
                        for ($i = 1; $i <= count($present); $i++)
                        {
                            $prs += $present[$i];
                        }
                        $sck = 0;
                        for ($i = 1; $i <= count($sick); $i++)
                        {
                            $sck += $sick[$i];
                        }
                        $lve = 0;
                        for ($i = 1; $i <= count($permit); $i++)
                        {
                            $lve += $permit[$i];
                        }
                        $abs = 0;
                        for ($i = 1; $i <= count($absent); $i++)
                        {
                            $abs += $absent[$i];
                        }
                    @endphp
                    <tfoot>
                        <tr>
                            <td class="text-center"><b>Total</b></td>
                            <td class="text-center">{{ $prs }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $sck }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $lve }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $abs }}</td>
                            <td class="text-center"></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        @endif
    </div>
</div>