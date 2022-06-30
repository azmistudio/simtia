@inject('scoreval', 'Modules\Academic\Repositories\Exam\ExamReportEloquent')
@php
	$tot_lesson = count($lessons);
    $tot_score_asp = count($score_aspects);
    $arr_tot_score_asp_class = array();
    $arr_tot_score_asp = array();
    $colspan = 0;
    for ($i = 0; $i < $tot_lesson; $i++)
    {
        $id_lesson = $arr_lessons[$i][0];
        $tot_aspect = count($arr_asp_lessons[$id_lesson]);
        for ($j = 0; $j < $tot_aspect; $j++)
        {
            $colspan += 1;
        }
    }
    $span_lesson = 3 + 1 * $colspan + $tot_score_asp;
@endphp
<html>
    <head>
        <title>LAPORAN LEGGER NILAI RAPOR KELAS</title>
        <style type="text/css">
            body { margin: 0; padding: 0; font-family: "Calibri", "Open Sans", serif !important; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            table.no-border, table.no-border th, table.no-border td { border: none; }
            table { border-collapse: collapse; border: 1px solid #000; }
            th, td { border-top: 1px solid #000; }
            .title { font-size: 13pt; font-weight: bold; }
            .subtitle { font-size: 11pt; font-weight: bold; }
        </style>
    </head>
    <body>
        <table class="no-border">
            <tbody>
                <tr>
                    <td colspan="{{ $span_lesson }}" align="center" class="title">{{ strtoupper(Session::get('institute')) }}</td>
                </tr>
                <tr>
                    <td colspan="{{ $span_lesson }}" align="center" class="title">LAPORAN LEGGER NILAI RAPOR KELAS</td>
                </tr>
                <tr>
                    <td colspan="{{ $span_lesson }}" align="center" class="subtitle">DEPARTEMEN {{ $requests->department }} - TAHUN AJARAN {{ $requests->schoolyear }} - KELAS {{ $requests->class }} - SEMESTER {{ $requests->semester }} - PELAJARAN {{ $requests->lesson }}</td>
                </tr>
            </tbody>
        </table>
        <br/>
        <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;">
            <thead>
                <tr style="background-color:#CCFFFF;">
                    <th class="text-center" rowspan="2">No.</th>
                    <th class="text-center" rowspan="2">NIS</th>
                    <th class="text-center" rowspan="2">Nama</th>
                    @for ($i=0; $i < $tot_lesson; $i++) 
                    @php
                        $id_lesson = $arr_lessons[$i][0];
                        $lesson = $arr_lessons[$i][1];
                        $tot_aspect = count($arr_asp_lessons[$id_lesson]);
                    @endphp
                    <th class="text-center" colspan="{{ $tot_aspect }}">{{ $lesson }}</th>
                    @endfor
                    <th class="text-center" colspan="{{ $tot_score_asp }}">Rata - Rata Santri</th>
                </tr>
                <tr style="background-color:#CCFFFF;">
                    @for ($i = 0; $i < $tot_lesson; $i++)
                    @php
                        $id_lesson = $arr_lessons[$i][0];
                        $tot_aspect = count($arr_asp_lessons[$id_lesson]);
                    @endphp
                    @for ($j = 0; $j < $tot_aspect; $j++)
                    @php
                        $id_aspect = $arr_asp_lessons[$id_lesson][$j];
                    @endphp
                    <th class="text-center">{{ $aspects[$id_aspect] }}</th>
                    @endfor
                    @endfor
                    @foreach ($score_aspects as $aspect)
                    <th class="text-center">{{ $aspect->basis }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($students as $student)
                @php $col_cnt = -1; @endphp
                <tr>
                    <td class="text-center">{{ $no }}</td>
                    <td class="text-center">{{ $student->student_no }}</td>
                    <td>{{ $student->student }}</td>
                    @for ($i = 0; $i < $tot_lesson; $i++)
                        @php
                            $id_lesson = $arr_lessons[$i][0];
                            $tot_aspect = count($arr_asp_lessons[$id_lesson]);
                        @endphp
                        @for ($j = 0; $j < $tot_aspect; $j++)
                            @php
                                $col_cnt += 1;
                                $id_aspect = $arr_asp_lessons[$id_lesson][$j];
                                $value = $scoreval->reportLeggerClassScore($student->id, $id_lesson, $requests->semester_id, $requests->class_id, $id_aspect);
                                // score lesson
                                if (!empty($value))
                                {
                                    if (array_key_exists($id_aspect, $arr_tot_score_asp))
                                    {
                                        $arr_tot_score_asp[$id_aspect][0] += $value->value;
                                        $arr_tot_score_asp[$id_aspect][1] += 1;
                                    } else {
                                        $arr_tot_score_asp[$id_aspect] = array($value->value, 1);
                                    }

                                    if ($no == 1)
                                    {
                                        $arr_tot_score_asp_class[$col_cnt] = array($value->value, 1);
                                    } else {
                                        $arr_tot_score_asp_class[$col_cnt][0] += $value->value;
                                        $arr_tot_score_asp_class[$col_cnt][1] += 1;
                                    }
                                    echo '<td class="text-center">'.$value->value.'</td>';
                                } else {
                                    echo '<td class="text-center">-</td>';
                                }
                            @endphp
                        @endfor
                    @endfor
                    {{-- avg --}}
                    @php
                        foreach ($score_aspects as $aspect)
                        {
                            $col_cnt += 1;
                            $score = '-';
                            $jscore = 0;
                            if (array_key_exists($aspect->id, $arr_tot_score_asp))
                            {
                                $score = $arr_tot_score_asp[$aspect->id][0];
                                $jscore = $arr_tot_score_asp[$aspect->id][1];
                            }
                            if ($jscore > 0)
                            {
                                $score = round($score/$jscore, 2);
                                if ($no == 1)
                                {
                                    $arr_tot_score_asp_class[$col_cnt] = array($score, 1);
                                } else {
                                    $arr_tot_score_asp_class[$col_cnt][0] += $score;
                                    $arr_tot_score_asp_class[$col_cnt][1] += 1;
                                }
                            }
                            echo '<td class="text-center">'.number_format($score,2).'</td>';
                        }
                    @endphp
                </tr>
                @php $no++; @endphp
                @endforeach
            </tbody>
            <tfoot>
                @php $jcol = count($arr_tot_score_asp_class); @endphp
                <tr>
                    <th class="text-right" colspan="3">Rata - Rata Kelas</th>
                    @for ($i = 0; $i < $jcol; $i++)
                    @php 
                        $avg = '';
                        if ($arr_tot_score_asp_class[$i][1] > 0)
                        {
                            $avg = round($arr_tot_score_asp_class[$i][0] / $arr_tot_score_asp_class[$i][1], 2);
                        }
                    @endphp
                    <th class="text-center">{{ $avg }}</th>
                    @endfor
                </tr>
            </tfoot>
        </table>
    </body>
</html>