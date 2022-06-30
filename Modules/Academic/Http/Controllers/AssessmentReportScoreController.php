<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\Department;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Exam;
use Modules\Academic\Entities\ExamReport;
use Modules\Academic\Entities\ExamReportComment;
use Modules\Academic\Entities\ExamReportScoreFinal;
use Modules\Academic\Entities\ExamReportScoreInfo;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\PresenceDaily;
use Modules\Academic\Entities\PresenceLesson;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use View;
use Exception;

class AssessmentReportScoreController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Rapor Santri';

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (!$request->ajax())
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        return view('academic::pages.assessments.assessment_report_scores', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexStudent(Request $request)
    {
        if (!$request->ajax())
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['socials'] = ExamReportComment::where('student_id', $request->student_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->orderBy('id')->get();
        $data['presences_daily'] = $this->getPresenceDaily($request->student_id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
        $data['presences_lesson'] = $this->getPresenceLessons($request->student_id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
        $data['lessons'] = $this->getLessons($request->student_id, $request->class_id, $request->semester_id);
        $data['aspects'] = $this->getAspects($request->student_id, $request->class_id, $request->semester_id);
        $lessons = $this->getLessonScores($request->student_id, $request->class_id, $request->semester_id);
        //
        $i = 0;
        foreach ($data['aspects'] as $aspect)
        {
            $aspect_arr[$i++] = array($aspect->id, $aspect->remark);
        }
        // tbody
        $prev_lesson_group = 0;
        $j = 1;
        $data['tbody_lesson_score'] = '';
        $data['tbody_lesson_score_desc'] = '';
        foreach ($lessons as $lesson)
        {
            // lesson group
            if ($prev_lesson_group != $lesson->group_id)
            {
                $prev_lesson_group = $lesson->group_id;
                $colspan = count($aspect_arr) * 2 + 3;
                $data['tbody_lesson_score'] .= '<tr><td style="background-color: #efefef;" colspan="'.$colspan.'"><b>' . ucwords($lesson->group) . '</b></td></tr>';
                $data['tbody_lesson_score_desc'] .= '<tr><td style="background-color: #efefef;" colspan="'.$colspan.'"><b>' . ucwords($lesson->group) . '</b></td></tr>';
            }
            // get kkm value
            $lesson_item = ExamReportScoreInfo::select('value')->where('lesson_id', $lesson->id)->where('semester_id', $request->semester_id)->where('class_id', $request->class_id)->first();

            $data['tbody_lesson_score'] .= '<tr>';
            $data['tbody_lesson_score'] .= '<td class="text-center">'.$j.'</td>';
            $data['tbody_lesson_score'] .= '<td>'.strtoupper($lesson->name).'</td>';
            // $data['tbody_lesson_score'] .= '<td class="text-center">'.$lesson_item->value.'</td>';

            $data['tbody_lesson_score_desc'] .= '<tr>';
            $data['tbody_lesson_score_desc'] .= '<td rowspan="'.count($aspect_arr).'" class="text-center">'.$j.'</td>';
            $data['tbody_lesson_score_desc'] .= '<td rowspan="'.count($aspect_arr).'">'.strtoupper($lesson->name).'</td>';
            $set_tr = false;

            // get exam score
            for ($i = 0; $i < count($aspect_arr); $i++)
            {
                $aspect = $aspect_arr[$i][0];
                $val_number = '';
                $val_letter = '';
                $comment = '';

                $values = $this->getValues($request->student_id, $lesson->id, $request->semester_id, $request->class_id, $aspect);

                if (count($values) > 0)
                {
                    $val_number = number_format($values[0]->value,2);
                    $val_letter = $values[0]->value_letter;
                    $comment = $values[0]->comment;
                }
                $data['tbody_lesson_score'] .= '<td class="text-center">'.$val_number.'</td>';
                $data['tbody_lesson_score'] .= '<td class="text-center">'.$val_letter.'</td>';

                $data['tbody_lesson_score_desc'] .= '<td class="">'.ucwords($aspect_arr[$i][1]).'</td>';
                $data['tbody_lesson_score_desc'] .= '<td class="">'.$comment.'</td>';
                $data['tbody_lesson_score_desc'] .= '</tr>';
                $set_tr = true;
            }
            $data['tbody_lesson_score'] .= '</tr>';
            $j++;
        }
        return view('academic::pages.assessments.assessment_report_scores_view', $data);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfStudent(Request $request)
    {
        $data['requests'] = $request->all();
        $data['socials'] = ExamReportComment::where('student_id', $request->student_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->orderBy('id')->get();
        $data['lessons'] = $this->getLessons($request->student_id, $request->class_id, $request->semester_id);
        $data['presences_daily'] = $this->getPresenceDaily($request->student_id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
        $data['presences_lesson'] = $this->getPresenceLessons($request->student_id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
        $data['profile'] = $this->getInstituteProfile();
        $data['aspects'] = $this->getAspects($request->student_id, $request->class_id, $request->semester_id);
        $lessons = $this->getLessonScores($request->student_id, $request->class_id, $request->semester_id);
        //
        $i = 0;
        foreach ($data['aspects'] as $aspect)
        {
            $aspect_arr[$i++] = array($aspect->id, $aspect->remark);
        }
        // tbody
        $prev_lesson_group = 0;
        $j = 1;
        $data['tbody_lesson_score'] = '';
        $data['tbody_lesson_score_desc'] = '';
        foreach ($lessons as $lesson)
        {
            // lesson group
            if ($prev_lesson_group != $lesson->group_id)
            {
                $prev_lesson_group = $lesson->group_id;
                $colspan = count($aspect_arr) * 2 + 3;
                $data['tbody_lesson_score'] .= '<tr><td style="background-color: #efefef;" colspan="'.$colspan.'"><b>' . ucwords($lesson->group) . '</b></td></tr>';
                $data['tbody_lesson_score_desc'] .= '<tr><td style="background-color: #efefef;" colspan="'.$colspan.'"><b>' . ucwords($lesson->group) . '</b></td></tr>';
            }
            // get kkm value
            $lesson_item = ExamReportScoreInfo::select('value')->where('lesson_id', $lesson->id)->where('semester_id', $request->semester_id)->where('class_id', $request->class_id)->first();

            $data['tbody_lesson_score'] .= '<tr>';
            $data['tbody_lesson_score'] .= '<td class="text-center">'.$j.'</td>';
            $data['tbody_lesson_score'] .= '<td>'.strtoupper($lesson->name).'</td>';
            // $data['tbody_lesson_score'] .= '<td class="text-center">'.$lesson_item->value.'</td>';

            $data['tbody_lesson_score_desc'] .= '<tr>';
            $data['tbody_lesson_score_desc'] .= '<td rowspan="'.count($aspect_arr).'" class="text-center">'.$j.'</td>';
            $data['tbody_lesson_score_desc'] .= '<td rowspan="'.count($aspect_arr).'">'.strtoupper($lesson->name).'</td>';
            $set_tr = false;

            // get exam score
            for ($i = 0; $i < count($aspect_arr); $i++)
            {
                $aspect = $aspect_arr[$i][0];
                $val_number = '';
                $val_letter = '';
                $comment = '';

                $values = $this->getValues($request->student_id, $lesson->id, $request->semester_id, $request->class_id, $aspect);

                if (count($values) > 0)
                {
                    $val_number = number_format($values[0]->value,2);
                    $val_letter = $values[0]->value_letter;
                    $comment = $values[0]->comment;
                }
                $data['tbody_lesson_score'] .= '<td class="text-center">'.$val_number.'</td>';
                $data['tbody_lesson_score'] .= '<td class="text-center">'.$val_letter.'</td>';

                $data['tbody_lesson_score_desc'] .= '<td class="">'.ucwords($aspect_arr[$i][1]).'</td>';
                $data['tbody_lesson_score_desc'] .= '<td class="">'.$comment.'</td>';
                $data['tbody_lesson_score_desc'] .= '</tr>';
                $set_tr = true;
            }
            $data['tbody_lesson_score'] .= '</tr>';
            $j++;
        }
        $view = View::make('academic::pages.assessments.assessment_report_student_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to Ms. Word Document.
     * @return Ms. Word
     */
    public function toWordStudent(Request $request)
    {
        set_time_limit(0);
        $socials = ExamReportComment::where('student_id', $request->student_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->orderBy('id')->get();
        $lessons = $this->getLessons($request->student_id, $request->class_id, $request->semester_id);
        $presences_daily = $this->getPresenceDaily($request->student_id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
        $presences_lesson = $this->getPresenceLessons($request->student_id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
        $semester = DB::table('academic.semesters')->where('id', $request->semester_id)->first();
        $teacher = DB::table('academic.classes')->select('employees.employee_id','employees.name')->where('academic.classes.id', $request->class_id)->join('employees','employees.id','=','academic.classes.employee_id')->first();
        $department = Department::select('employees.employee_id','employees.name')->where('departments.id', $semester->department_id)->join('employees','employees.id','=','departments.employee_id')->first();
        $aspects = $this->getAspects($request->student_id, $request->class_id, $request->semester_id);
        $lesson_scores = $this->getLessonScores($request->student_id, $request->class_id, $request->semester_id);
        $profile = $this->getInstituteProfile();
        $address = '<p>'.$profile['address'] . '</p><p>Telp.' . $profile['phone'] . ' - Fax. ' . $profile['fax'] . '</p><p>Website: ' . $profile['web'] . ' - Email: ' . $profile['email'] . '</p>';
        $present = array();
        $sick = array();
        $permit = array();
        $absent = array();
        //
        $phpWord = new PhpWord();
        $sectionStyle = array('marginLeft' => Converter::cmToTwip(1.91), 'marginRight' => Converter::cmToTwip(1.91));
        $section = $phpWord->addSection($sectionStyle);
        //
        $titleStyle = 'titleStyle';
        $subTitleStyle = 'subTitleStyle';
        $bodyStyle = 'bodyStyle';
        $phpWord->addFontStyle($titleStyle, array('name' => 'Arial', 'size' => 12, 'bold' => true));
        $phpWord->addFontStyle($subTitleStyle, array('name' => 'Arial', 'size' => 11, 'bold' => true));
        $phpWord->addFontStyle($bodyStyle, array('name' => 'Arial', 'size' => 11));
        $tableStyleHeader = array('cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $tableStyle = array('borderSize' => 1, 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $tableStyleLeft = array('borderSize' => 1, 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellHLeft = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT);
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $cellVCentered = array('valign' => 'center');

        $table = $section->addTable($tableStyleHeader);
        $table->addRow();
        $cell1 = $table->addCell(2000, $cellRowSpan);
        $textrun1 = $cell1->addTextRun($cellHCentered);
        $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 

        $textrun1->addImage($logo, array('width' => 60,'height' => 60));
        $cell2 = $table->addCell(7800, $cellColSpan);
        $textrun2 = $cell2->addTextRun($cellHLeft);
        $textrun2->addText(Str::upper($request->session()->get('institute')), $titleStyle);
        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table_address = $table->addCell(7800, $cellVCentered);
        \PhpOffice\PhpWord\Shared\Html::addHtml($table_address, $address, false, false, null);

        $section->addTextBreak(2, null, null);
        //
        $table2 = $section->addTable($tableStyle);
        $table2->addRow();

        $cell3 = $table2->addCell(10000, $cellColSpan);
        $textrun3 = $cell3->addTextRun($cellHCentered);
        $textrun3->addText('Laporan Hasil Belajar', $subTitleStyle);

        $table2->addRow();
        $table2->addCell(2000, $cellVCentered)->addText('Departemen', null, $cellHLeft);
        $table2->addCell(6000, $cellVCentered)->addText(': ' . $request->department, null, $cellHLeft);
        $table2->addRow();
        $table2->addCell(2000, $cellVCentered)->addText('Tahun Ajaran', null, $cellHLeft);
        $table2->addCell(6000, $cellVCentered)->addText(': ' . $request->schoolyear, null, $cellHLeft);
        $table2->addRow();
        $table2->addCell(2000, $cellVCentered)->addText('Semester', null, $cellHLeft);
        $table2->addCell(6000, $cellVCentered)->addText(': ' . $request->semester, null, $cellHLeft);
        $table2->addRow();
        $table2->addCell(2000, $cellVCentered)->addText('Tingkat/Kelas', null, $cellHLeft);
        $table2->addCell(6000, $cellVCentered)->addText(': ' . $request->grade .' - '. $request->class, null, $cellHLeft);
        $table2->addRow();
        $table2->addCell(2000, $cellVCentered)->addText('NIS', null, $cellHLeft);
        $table2->addCell(6000, $cellVCentered)->addText(': ' . $request->student_no, null, $cellHLeft);
        $table2->addRow();
        $table2->addCell(2000, $cellVCentered)->addText('Nama', null, $cellHLeft);
        $table2->addCell(6000, $cellVCentered)->addText(': ' . $request->student_name, null, $cellHLeft);

        $section->addTextBreak(1, null, null);
        //
        if (count($socials) > 0)
        {
            foreach ($socials as $social)
            {
                $section->addText('Sikap ' . ucfirst($social->aspect), $subTitleStyle, null);

                $tableSocial = $section->addTable($tableStyle);
                $tableSocial->addRow();
                $tableSocial->addCell(2500, $cellVCentered)->addText('Predikat: ' . $social->getType->name, null, $cellHLeft);
                $tableSocial_comment = $tableSocial->addCell(8000, $cellVCentered);
                \PhpOffice\PhpWord\Shared\Html::addHtml($tableSocial_comment, html_entity_decode(str_replace('<br>', '<br/>', $social->comment)), false, false, null);
                $section->addTextBreak(1, null, null);
            }
        }
        //
        $section->addText('Nilai Pelajaran', $subTitleStyle, null);

        $tableScore = $section->addTable($tableStyleLeft);
        $row = $tableScore->addRow();
        $row->addCell(700, array('vMerge' => 'restart', 'valign' => 'center'))->addText('No.', $subTitleStyle, $cellHCentered);
        $row->addCell(3000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Pelajaran', $subTitleStyle, $cellHCentered);
        // $row->addCell(1000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('KKM', $subTitleStyle, $cellHCentered);

        $i = 0;
        $aspect_arr = array();
        foreach ($aspects as $aspect)
        {
            $aspect_arr[$i++] = array($aspect->id, $aspect->remark);
            $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText(ucwords($aspect->remark), $subTitleStyle, $cellHCentered);
        }
        $row = $tableScore->addRow();
        $row->addCell(1000, array('vMerge' => 'continue'));
        $row->addCell(1000, array('vMerge' => 'continue'));
        foreach ($aspect_arr as $column)
        {
            $row->addCell(2000, $cellVCentered)->addText('Nilai', $subTitleStyle, $cellHCentered);
            $row->addCell(2000, $cellVCentered)->addText('Predikat', $subTitleStyle, $cellHCentered);
        }
        $row = $tableScore->addRow();

        $prev_lesson_group = 0;
        $j = 1;
        foreach ($lesson_scores as $lesson)
        {
            $lesson_item = ExamReportScoreInfo::where('lesson_id', $lesson->id)->where('semester_id', $request->semester_id)->where('class_id', $request->class_id)->first();
            if ($prev_lesson_group != $lesson->group_id)
            {
                $prev_lesson_group = $lesson->group_id;
                $colspan = count($aspect_arr) * 2 + 2;
                $row->addCell(2000, array('gridSpan' => $colspan, 'vMerge' => 'restart'))->addText(ucwords($lesson->group), $subTitleStyle, $cellHLeft);
                $row = $tableScore->addRow();
                if ($lesson_item->lesson_id == $lesson->id)
                {
                    $row->addCell(700)->addText($j, null, $cellHCentered);
                    $row->addCell(3000)->addText(strtoupper($lesson->name), null, $cellHLeft);
                }
                for ($i = 0; $i < count($aspect_arr); $i++)
                {
                    $aspect = $aspect_arr[$i][0];
                    $val_number = '';
                    $val_letter = '';
                    $comment = '';

                    $values = $this->getValues($request->student_id, $lesson->id, $request->semester_id, $request->class_id, $aspect);
                    if (count($values) > 0)
                    {
                        $val_number = number_format($values[0]->value,2);
                        $val_letter = $values[0]->value_letter;
                        $comment = $values[0]->comment;
                    }
                    $row->addCell(2000)->addText($val_number, null, $cellHCentered);
                    $row->addCell(2000)->addText($val_letter, null, $cellHCentered);
                }
                $row = $tableScore->addRow();
            }
            $j++;
        }

        $section->addTextBreak(1, null, null);
        //
        $section->addText('Deskripsi Nilai Pelajaran', $subTitleStyle, null);

        $tableDesc = $section->addTable($tableStyle);
        $row = $tableDesc->addRow();
        $row->addCell(700, null)->addText('No.', $subTitleStyle, $cellHCentered);
        $row->addCell(3000, null)->addText('Pelajaran', $subTitleStyle, $cellHCentered);
        $row->addCell(2000, null)->addText('Aspek', $subTitleStyle, $cellHCentered);
        $row->addCell(4300, null)->addText('Deskripsi', $subTitleStyle, $cellHCentered);

        $row = $tableDesc->addRow();

        $next_lesson_group = 0;
        $k = 1;
        foreach ($lesson_scores as $lesson)
        {
            $lesson_item = ExamReportScoreInfo::where('lesson_id', $lesson->id)->where('semester_id', $request->semester_id)->where('class_id', $request->class_id)->first();
            if ($next_lesson_group != $lesson->group_id)
            {
                $next_lesson_group = $lesson->group_id;
                $colspan = count($aspect_arr) * 2 + 3;
                $row->addCell(2000, array('gridSpan' => $colspan, 'vMerge' => 'restart'))->addText(ucwords($lesson->group), $subTitleStyle, $cellHLeft);
                $row = $tableDesc->addRow();
                if ($lesson_item->lesson_id == $lesson->id)
                {
                    $row->addCell(700)->addText($k, array('vMerge' => 'restart'), $cellHCentered);
                    $row->addCell(2000)->addText(strtoupper($lesson->name), array('vMerge' => 'restart'), $cellHLeft);
                }
                $set_rowspan = false;
                for ($i = 0; $i < count($aspect_arr); $i++)
                {
                    $aspect = $aspect_arr[$i][0];
                    $comment = '';

                    $values = $this->getValues($request->student_id, $lesson->id, $request->semester_id, $request->class_id, $aspect);
                    if (count($values) > 0)
                    {
                        $comment = $values[0]->comment;
                    }

                    if ($set_rowspan)
                    {
                        $row = $tableDesc->addRow();
                        $row->addCell(700, array('vMerge' => 'continue'));
                        $row->addCell(2000, array('vMerge' => 'continue'));
                    }
                    $row->addCell(3000)->addText(ucwords($aspect_arr[$i][1]), null, $cellHLeft);
                    $row_comment = $row->addCell(3000);
                    \PhpOffice\PhpWord\Shared\Html::addHtml($row_comment, html_entity_decode(str_replace('<br>', '<br/>', $comment)), false, false, null);

                    $set_rowspan = true;
                }
            }
            $row = $tableDesc->addRow();
            $k++;
        }

        //
        $section->addTextBreak(2, null, null);

        $tableFooter = $section->addTable($tableStyleHeader);
        $row = $tableFooter->addRow();
        $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Orang Tua/Wali Santri', $subTitleStyle, $cellHCentered);
        $row->addCell(8000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Mengetahui,', $subTitleStyle, $cellHCentered);
        $row = $tableFooter->addRow();
        $row->addCell(2000, array('vMerge' => 'continue'));
        $row->addCell(4000, $cellVCentered)->addText('Kepala Sekolah', $subTitleStyle, $cellHCentered);
        $row->addCell(4000, $cellVCentered)->addText('Wali Kelas', $subTitleStyle, $cellHCentered);
        $row = $tableFooter->addRow();
        $row->addCell(2000, null)->addTextBreak(2, null, null);
        $row->addCell(4000, null);
        $row->addCell(4000, null);
        $row = $tableFooter->addRow();
        $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('(___________________________________)', null, $cellHCentered);
        $row->addCell(4000, null)->addText($this->getEmployeeName($department->employee_id), null, $cellHCentered);
        $row->addCell(4000, null)->addText($this->getEmployeeName($teacher->employee_id), null, $cellHCentered);
        $row = $tableFooter->addRow();
        $row->addCell(2000, array('vMerge' => 'continue'));
        $row->addCell(4000, null)->addText('NIP: ' . $department->employee_id, null, $cellHCentered);
        $row->addCell(4000, null)->addText('NIP: ' . $teacher->employee_id, null, $cellHCentered);

        //
        if ($request->daily == "true")
        {
            $section->addPageBreak();

            $tableHead = $section->addTable($tableStyle);
            $tableHead->addRow();

            $cell3 = $tableHead->addCell(10000, $cellColSpan);
            $textrun3 = $cell3->addTextRun($cellHCentered);
            $textrun3->addText('Presensi Harian', $subTitleStyle);

            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Departemen', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->department, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Tahun Ajaran', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->schoolyear, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Semester', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->semester, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Tingkat/Kelas', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->grade .' - '. $request->class, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('NIS', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->student_no, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Nama', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->student_name, null, $cellHLeft);

            $section->addTextBreak(2, null, null);

            $tablePresence = $section->addTable($tableStyle);
            $row = $tablePresence->addRow();
            $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Hadir', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Sakit', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Ijin', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Alpa', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Cuti', $subTitleStyle, $cellHCentered);

            $row = $tablePresence->addRow();
            $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);

            $row = $tablePresence->addRow();

            $row->addCell(3000, $cellVCentered)->addText($presences_daily->present, $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText($presences_daily->present != 0 && $presences_daily->total != 0 ? round(($presences_daily->present / $presences_daily->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText($presences_daily->sick, $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText($presences_daily->sick != 0 && $presences_daily->total != 0 ? round(($presences_daily->sick / $presences_daily->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText($presences_daily->permit, $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText($presences_daily->permit != 0 && $presences_daily->total != 0 ? round(($presences_daily->permit / $presences_daily->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText($presences_daily->absent, $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText($presences_daily->absent != 0 && $presences_daily->total != 0 ? round(($presences_daily->absent / $presences_daily->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
            $row->addCell(3000, $cellVCentered)->addText($presences_daily->leave, $subTitleStyle, $cellHCentered);
            $row->addCell(1000, $cellVCentered)->addText($presences_daily->leave != 0 && $presences_daily->total != 0 ? round(($presences_daily->leave / $presences_daily->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);

            $section->addTextBreak(2, null, null);

            $tableFooter = $section->addTable($tableStyleHeader);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Orang Tua/Wali Santri', $subTitleStyle, $cellHCentered);
            $row->addCell(8000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Mengetahui,', $subTitleStyle, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'continue'));
            $row->addCell(4000, $cellVCentered)->addText('Kepala Sekolah', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, $cellVCentered)->addText('Wali Kelas', $subTitleStyle, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, null)->addTextBreak(2, null, null);
            $row->addCell(4000, null);
            $row->addCell(4000, null);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('(___________________________________)', null, $cellHCentered);
            $row->addCell(4000, null)->addText($this->getEmployeeName($department->employee_id), null, $cellHCentered);
            $row->addCell(4000, null)->addText($this->getEmployeeName($teacher->employee_id), null, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'continue'));
            $row->addCell(4000, null)->addText('NIP: ' . $department->employee_id, null, $cellHCentered);
            $row->addCell(4000, null)->addText('NIP: ' . $teacher->employee_id, null, $cellHCentered);
        }

        if ($request->lesson == "true")
        {
            $section->addPageBreak();

            $tableHead = $section->addTable($tableStyle);
            $tableHead->addRow();

            $cell3 = $tableHead->addCell(10000, $cellColSpan);
            $textrun3 = $cell3->addTextRun($cellHCentered);
            $textrun3->addText('Presensi Pelajaran', $subTitleStyle);

            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Departemen', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->department, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Tahun Ajaran', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->schoolyear, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Semester', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->semester, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Tingkat/Kelas', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->grade .' - '. $request->class, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('NIS', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->student_no, null, $cellHLeft);
            $tableHead->addRow();
            $tableHead->addCell(2000, $cellVCentered)->addText('Nama', null, $cellHLeft);
            $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->student_name, null, $cellHLeft);

            $section->addTextBreak(2, null, null);

            $tableLesson = $section->addTable($tableStyle);
            $row = $tableLesson->addRow();
            $row->addCell(3000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Pelajaran', $subTitleStyle, $cellHCentered);
            $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Hadir', $subTitleStyle, $cellHCentered);
            $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Sakit', $subTitleStyle, $cellHCentered);
            $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Ijin', $subTitleStyle, $cellHCentered);
            $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Alpa', $subTitleStyle, $cellHCentered);

            $row = $tableLesson->addRow();
            $row->addCell(3000, array('vMerge' => 'continue'));
            $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
            $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);

            foreach ($lessons as $lesson)
            {
                $row = $tableLesson->addRow();
                $row->addCell(3000, $cellVCentered)->addText(strtoupper($lesson->lesson), $subTitleStyle, $cellHLeft);
                $counter = 1;
                foreach ($presences_lesson as $presence)
                {
                    if ($presence->lesson_id == $lesson->lesson_id)
                    {
                        $present[$counter] = $presence->present;
                        $sick[$counter] = $presence->sick;
                        $permit[$counter] = $presence->permit;
                        $absent[$counter] = $presence->absent;
                        $row->addCell(1500, $cellVCentered)->addText($presence->present, $subTitleStyle, $cellHCentered);
                        $row->addCell(500, $cellVCentered)->addText($presence->present != 0 && $presence->total != 0 ? round(($presence->present / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(1500, $cellVCentered)->addText($presence->sick, $subTitleStyle, $cellHCentered);
                        $row->addCell(500, $cellVCentered)->addText($presence->sick != 0 && $presence->total != 0 ? round(($presence->sick / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(1500, $cellVCentered)->addText($presence->permit, $subTitleStyle, $cellHCentered);
                        $row->addCell(500, $cellVCentered)->addText($presence->permit != 0 && $presence->total != 0 ? round(($presence->permit / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(1500, $cellVCentered)->addText($presence->absent, $subTitleStyle, $cellHCentered);
                        $row->addCell(500, $cellVCentered)->addText($presence->absent != 0 && $presence->total != 0 ? round(($presence->absent / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                    }
                    $counter++;
                }
            }

            $prs = 0;
            for ($i = 1; $i < count($present); $i++)
            {
                $prs += $present[$i];
            }
            $sck = 0;
            for ($i = 1; $i < count($sick); $i++)
            {
                $sck += $sick[$i];
            }
            $lve = 0;
            for ($i = 1; $i < count($permit); $i++)
            {
                $lve += $permit[$i];
            }
            $abs = 0;
            for ($i = 1; $i < count($absent); $i++)
            {
                $abs += $absent[$i];
            }
            $row = $tableLesson->addRow();
            $row->addCell(3000, $cellVCentered)->addText('Total', $subTitleStyle, $cellHCentered);
            $row->addCell(1500, $cellVCentered)->addText($prs, $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText(null);
            $row->addCell(1500, $cellVCentered)->addText($sck, $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText(null);
            $row->addCell(1500, $cellVCentered)->addText($lve, $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText(null);
            $row->addCell(1500, $cellVCentered)->addText($abs, $subTitleStyle, $cellHCentered);
            $row->addCell(500, $cellVCentered)->addText(null);

            $section->addTextBreak(2, null, null);

            $tableFooter = $section->addTable($tableStyleHeader);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Orang Tua/Wali Santri', $subTitleStyle, $cellHCentered);
            $row->addCell(8000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Mengetahui,', $subTitleStyle, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'continue'));
            $row->addCell(4000, $cellVCentered)->addText('Kepala Sekolah', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, $cellVCentered)->addText('Wali Kelas', $subTitleStyle, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, null)->addTextBreak(2, null, null);
            $row->addCell(4000, null);
            $row->addCell(4000, null);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('(___________________________________)', null, $cellHCentered);
            $row->addCell(4000, null)->addText($this->getEmployeeName($department->employee_id), null, $cellHCentered);
            $row->addCell(4000, null)->addText($this->getEmployeeName($teacher->employee_id), null, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'continue'));
            $row->addCell(4000, null)->addText('NIP: ' . $department->employee_id, null, $cellHCentered);
            $row->addCell(4000, null)->addText('NIP: ' . $teacher->employee_id, null, $cellHCentered);
        }
        $filename = date('Ymdhis') . '_' . 'simtia_rapor_santri.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /**
     * Export resource to Ms. Word Document.
     * @return Ms. Word
     */
    public function toWord(Request $request)
    {
        set_time_limit(0);
        $semester = DB::table('academic.semesters')->where('id', $request->semester_id)->first();
        $teacher = DB::table('academic.classes')->select('employees.id','employees.employee_id','employees.name')->where('academic.classes.id', $request->class_id)->join('employees','employees.id','=','academic.classes.employee_id')->first();
        $department = Department::select('employees.id','employees.employee_id','employees.name')->where('departments.id', $semester->department_id)->join('employees','employees.id','=','departments.employee_id')->first();
        $profile = $this->getInstituteProfile();
        $address = '<p>'.$profile['address'] . '</p><p>Telp.' . $profile['phone'] . ' - Fax. ' . $profile['fax'] . '</p><p>Website: ' . $profile['web'] . ' - Email: ' . $profile['email'] . '</p>';
        $present = array();
        $sick = array();
        $permit = array();
        $absent = array();
        //
        $phpWord = new PhpWord();
        $sectionStyle = array('marginLeft' => Converter::cmToTwip(1.91), 'marginRight' => Converter::cmToTwip(1.91));
        $section = $phpWord->addSection($sectionStyle);
        $titleStyle = 'titleStyle';
        $subTitleStyle = 'subTitleStyle';
        $bodyStyle = 'bodyStyle';
        $phpWord->addFontStyle($titleStyle, array('name' => 'Arial', 'size' => 12, 'bold' => true));
        $phpWord->addFontStyle($subTitleStyle, array('name' => 'Arial', 'size' => 11, 'bold' => true));
        $phpWord->addFontStyle($bodyStyle, array('name' => 'Arial', 'size' => 11));
        $tableStyleHeader = array('cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $tableStyle = array('borderSize' => 1, 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $tableStyleLeft = array('borderSize' => 1, 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellHLeft = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT);
        $cellHCentered = array('alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER);
        $cellVCentered = array('valign' => 'center');
        // header
        $table = $section->addTable($tableStyleHeader);
        $table->addRow();
        $cell1 = $table->addCell(2000, $cellRowSpan);
        $textrun1 = $cell1->addTextRun($cellHCentered);
        $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
        $textrun1->addImage($logo, array('width' => 60,'height' => 60));
        $cell2 = $table->addCell(7800, $cellColSpan);
        $textrun2 = $cell2->addTextRun($cellHLeft);
        $textrun2->addText(Str::upper($profile['name']), $titleStyle);
        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table_address = $table->addCell(7800, $cellVCentered);
        \PhpOffice\PhpWord\Shared\Html::addHtml($table_address, $address, false, false, null);
        $section->addTextBreak(2, null, null);
        //
        $students = Students::where('class_id', $request->class_id)->get();
        foreach ($students as $student)
        {
            $aspects = $this->getAspects($student->id, $request->class_id, $request->semester_id);
            $lesson_scores = $this->getLessonScores($student->id, $request->class_id, $request->semester_id);
            //
            $tableInfo = $section->addTable($tableStyle);
            $tableInfo->addRow();

            $cellInfo = $tableInfo->addCell(10000, $cellColSpan);
            $textrunInfo = $cellInfo->addTextRun($cellHCentered);
            $textrunInfo->addText('Laporan Hasil Belajar', $subTitleStyle);

            $tableInfo->addRow();
            $tableInfo->addCell(2000, $cellVCentered)->addText('Departemen', null, $cellHLeft);
            $tableInfo->addCell(6000, $cellVCentered)->addText(': ' . $request->department, null, $cellHLeft);
            $tableInfo->addRow();
            $tableInfo->addCell(2000, $cellVCentered)->addText('Tahun Ajaran', null, $cellHLeft);
            $tableInfo->addCell(6000, $cellVCentered)->addText(': ' . $request->schoolyear, null, $cellHLeft);
            $tableInfo->addRow();
            $tableInfo->addCell(2000, $cellVCentered)->addText('Semester', null, $cellHLeft);
            $tableInfo->addCell(6000, $cellVCentered)->addText(': ' . $request->semester, null, $cellHLeft);
            $tableInfo->addRow();
            $tableInfo->addCell(2000, $cellVCentered)->addText('Tingkat/Kelas', null, $cellHLeft);
            $tableInfo->addCell(6000, $cellVCentered)->addText(': ' . $request->grade .' - '. $request->class, null, $cellHLeft);
            $tableInfo->addRow();
            $tableInfo->addCell(2000, $cellVCentered)->addText('NIS', null, $cellHLeft);
            $tableInfo->addCell(6000, $cellVCentered)->addText(': ' . $student->student_no, null, $cellHLeft);
            $tableInfo->addRow();
            $tableInfo->addCell(2000, $cellVCentered)->addText('Nama', null, $cellHLeft);
            $tableInfo->addCell(6000, $cellVCentered)->addText(': ' . $student->name, null, $cellHLeft);

            $section->addTextBreak(2, null, null);
            //
            $socials = ExamReportComment::where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->orderBy('id')->get();
            foreach ($socials as $social)
            {
                if ($social->student_id == $student->id)
                {
                    $section->addText('Sikap ' . ucfirst($social->aspect), $subTitleStyle, null);

                    $tableSocial = $section->addTable($tableStyle);
                    $tableSocial->addRow();
                    $tableSocial->addCell(2000, $cellVCentered)->addText('Predikat: ' . $social->getType->name, null, $cellHLeft);
                    $tableSocial_comment = $tableSocial->addCell(8000, $cellVCentered);
                    \PhpOffice\PhpWord\Shared\Html::addHtml($tableSocial_comment, html_entity_decode(str_replace('<br>', '<br/>', $social->comment)), false, false, null);

                    $section->addTextBreak(1, null, null);
                }
            }

            //
            $lessons = $this->getLessons(0, $request->class_id, $request->semester_id);

            $section->addText('Nilai Pelajaran', $subTitleStyle, null);

            $tableScore = $section->addTable($tableStyleLeft);
            $row = $tableScore->addRow();
            $row->addCell(700, array('vMerge' => 'restart', 'valign' => 'center'))->addText('No.', $subTitleStyle, $cellHCentered);
            $row->addCell(3000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Pelajaran', $subTitleStyle, $cellHCentered);
            // $row->addCell(1000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('KKM', $subTitleStyle, $cellHCentered);

            $i = 0;
            $aspect_arr = array();
            foreach ($aspects as $aspect)
            {
                $aspect_arr[$i++] = array($aspect->id, $aspect->remark);
                $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText(ucwords($aspect->remark), $subTitleStyle, $cellHCentered);
            }
            $row = $tableScore->addRow();
            $row->addCell(1000, array('vMerge' => 'continue'));
            $row->addCell(1000, array('vMerge' => 'continue'));
            // $row->addCell(1000, array('vMerge' => 'continue'));
            foreach ($aspect_arr as $column)
            {
                $row->addCell(2000, $cellVCentered)->addText('Nilai', $subTitleStyle, $cellHCentered);
                $row->addCell(2000, $cellVCentered)->addText('Predikat', $subTitleStyle, $cellHCentered);
            }

            $row = $tableScore->addRow();

            $prev_lesson_group = 0;
            $j = 1;
            foreach ($lesson_scores as $lesson)
            {
                if ($lesson->student_id == $student->id)
                {
                    $lesson_item = ExamReportScoreInfo::where('lesson_id', $lesson->id)->where('semester_id', $request->semester_id)->where('class_id', $request->class_id)->first();
                    if ($prev_lesson_group != $lesson->group_id)
                    {
                        $prev_lesson_group = $lesson->group_id;
                        $colspan = count($aspect_arr) * 2 + 3;
                        $row->addCell(2000, array('gridSpan' => $colspan, 'vMerge' => 'restart'))->addText(ucwords($lesson->group), $subTitleStyle, $cellHLeft);
                        $row = $tableScore->addRow();
                        if ($lesson_item->lesson_id == $lesson->id)
                        {
                            $row->addCell(700)->addText($j, null, $cellHCentered);
                            $row->addCell(3000)->addText(strtoupper($lesson->name), null, $cellHLeft);
                        }
                        for ($i = 0; $i < count($aspect_arr); $i++)
                        {
                            $aspect = $aspect_arr[$i][0];
                            $val_number = '';
                            $val_letter = '';
                            $comment = '';

                            $values = $this->getValues($student->id, $lesson->id, $request->semester_id, $request->class_id, $aspect);
                            if (count($values) > 0)
                            {
                                $val_number = number_format($values[0]->value,2);
                                $val_letter = $values[0]->value_letter;
                                $comment = $values[0]->comment;
                            }

                            $row->addCell(2000)->addText($val_number, null, $cellHCentered);
                            $row->addCell(2000)->addText($val_letter, null, $cellHCentered);
                        }
                        $row = $tableScore->addRow();
                    }
                    $j++;
                }
            }

            $section->addTextBreak(1, null, null);
            //
            $section->addText('Deskripsi Nilai Pelajaran', $subTitleStyle, null);

            $tableDesc = $section->addTable($tableStyle);
            $row = $tableDesc->addRow();
            $row->addCell(700, null)->addText('No.', $subTitleStyle, $cellHCentered);
            $row->addCell(3000, null)->addText('Pelajaran', $subTitleStyle, $cellHCentered);
            $row->addCell(2000, null)->addText('Aspek', $subTitleStyle, $cellHCentered);
            $row->addCell(4300, null)->addText('Deskripsi', $subTitleStyle, $cellHCentered);

            $row = $tableDesc->addRow();

            $next_lesson_group = 0;
            $k = 1;
            foreach ($lesson_scores as $lesson)
            {
                if ($lesson->student_id == $student->id)
                {
                    $lesson_item = ExamReportScoreInfo::where('lesson_id', $lesson->id)->where('semester_id', $request->semester_id)->where('class_id', $request->class_id)->first();
                    if ($next_lesson_group != $lesson->group_id)
                    {
                        $next_lesson_group = $lesson->group_id;
                        $colspan = count($aspect_arr) * 2 + 2;
                        $row->addCell(2000, array('gridSpan' => $colspan, 'vMerge' => 'restart'))->addText(ucwords($lesson->group), $subTitleStyle, $cellHLeft);
                        $row = $tableDesc->addRow();
                        if ($lesson_item->lesson_id == $lesson->id)
                        {
                            $row->addCell(700)->addText($k, array('vMerge' => 'restart'), $cellHCentered);
                            $row->addCell(2000)->addText(strtoupper($lesson->name), array('vMerge' => 'restart'), $cellHLeft);
                        }
                        $set_rowspan = false;
                        for ($i = 0; $i < count($aspect_arr); $i++)
                        {
                            $aspect = $aspect_arr[$i][0];
                            $comment = '';

                            $values = $this->getValues($student->id, $lesson->id, $request->semester_id, $request->class_id, $aspect);
                            if (count($values) > 0)
                            {
                                $comment = $values[0]->comment;
                            }

                            if ($set_rowspan)
                            {
                                $row = $tableDesc->addRow();
                                $row->addCell(700, array('vMerge' => 'continue'));
                                $row->addCell(2000, array('vMerge' => 'continue'));
                            }
                            $row->addCell(3000)->addText(ucwords($aspect_arr[$i][1]), null, $cellHLeft);
                            $row_comment = $row->addCell(3000);
                            \PhpOffice\PhpWord\Shared\Html::addHtml($row_comment, html_entity_decode(str_replace('<br>', '<br/>', $comment)), false, false, null);

                            $set_rowspan = true;
                        }
                        $row = $tableDesc->addRow();
                    }
                    $k++;
                }
            }
            //
            $section->addTextBreak(2, null, null);

            $tableFooter = $section->addTable($tableStyleHeader);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Orang Tua/Wali Santri', $subTitleStyle, $cellHCentered);
            $row->addCell(8000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Mengetahui,', $subTitleStyle, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'continue'));
            $row->addCell(4000, $cellVCentered)->addText('Kepala Sekolah', $subTitleStyle, $cellHCentered);
            $row->addCell(4000, $cellVCentered)->addText('Wali Kelas', $subTitleStyle, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, null)->addTextBreak(2, null, null);
            $row->addCell(4000, null);
            $row->addCell(4000, null);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('(___________________________________)', null, $cellHCentered);
            $row->addCell(4000, null)->addText($this->getEmployeeName($department->id), null, $cellHCentered);
            $row->addCell(4000, null)->addText($this->getEmployeeName($teacher->id), null, $cellHCentered);
            $row = $tableFooter->addRow();
            $row->addCell(2000, array('vMerge' => 'continue'));
            $row->addCell(4000, null)->addText('NIP: ' . $department->employee_id, null, $cellHCentered);
            $row->addCell(4000, null)->addText('NIP: ' . $teacher->employee_id, null, $cellHCentered);

            if ($request->daily == "true")
            {
                $presences_daily = $this->getPresenceDaily(0, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
                $section->addPageBreak();

                $tableHead = $section->addTable($tableStyle);
                $tableHead->addRow();

                $cell3 = $tableHead->addCell(10000, $cellColSpan);
                $textrun3 = $cell3->addTextRun($cellHCentered);
                $textrun3->addText('Presensi Harian', $subTitleStyle);

                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Departemen', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->department, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Tahun Ajaran', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->schoolyear, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Semester', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->semester, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Tingkat/Kelas', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->grade .' - '. $request->class, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('NIS', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $student->student_no, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Nama', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $student->name, null, $cellHLeft);

                $section->addTextBreak(2, null, null);

                $tablePresence = $section->addTable($tableStyle);
                $row = $tablePresence->addRow();
                $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Hadir', $subTitleStyle, $cellHCentered);
                $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Sakit', $subTitleStyle, $cellHCentered);
                $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Ijin', $subTitleStyle, $cellHCentered);
                $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Alpa', $subTitleStyle, $cellHCentered);
                $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Cuti', $subTitleStyle, $cellHCentered);

                $row = $tablePresence->addRow();
                $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(3000, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(1000, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);

                $row = $tablePresence->addRow();

                foreach ($presences_daily as $presence)
                {
                    if ($presence->student_id == $student->id)
                    {
                        $row->addCell(3000, $cellVCentered)->addText($presence->present, $subTitleStyle, $cellHCentered);
                        $row->addCell(1000, $cellVCentered)->addText($presence->present != 0 && $presence->total != 0 ? round(($presence->present / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(3000, $cellVCentered)->addText($presence->sick, $subTitleStyle, $cellHCentered);
                        $row->addCell(1000, $cellVCentered)->addText($presence->sick != 0 && $presence->total != 0 ? round(($presence->sick / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(3000, $cellVCentered)->addText($presence->permit, $subTitleStyle, $cellHCentered);
                        $row->addCell(1000, $cellVCentered)->addText($presence->permit != 0 && $presence->total != 0 ? round(($presence->permit / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(3000, $cellVCentered)->addText($presence->absent, $subTitleStyle, $cellHCentered);
                        $row->addCell(1000, $cellVCentered)->addText($presence->absent != 0 && $presence->total != 0 ? round(($presence->absent / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                        $row->addCell(3000, $cellVCentered)->addText($presence->leave, $subTitleStyle, $cellHCentered);
                        $row->addCell(1000, $cellVCentered)->addText($presence->leave != 0 && $presence->total != 0 ? round(($presence->leave / $presences_daily->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);

                        $section->addTextBreak(2, null, null);

                        $tableFooter = $section->addTable($tableStyleHeader);
                        $row = $tableFooter->addRow();
                        $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Orang Tua/Wali Santri', $subTitleStyle, $cellHCentered);
                        $row->addCell(8000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Mengetahui,', $subTitleStyle, $cellHCentered);
                        $row = $tableFooter->addRow();
                        $row->addCell(2000, array('vMerge' => 'continue'));
                        $row->addCell(4000, $cellVCentered)->addText('Kepala Sekolah', $subTitleStyle, $cellHCentered);
                        $row->addCell(4000, $cellVCentered)->addText('Wali Kelas', $subTitleStyle, $cellHCentered);
                        $row = $tableFooter->addRow();
                        $row->addCell(2000, null)->addTextBreak(2, null, null);
                        $row->addCell(4000, null);
                        $row->addCell(4000, null);
                        $row = $tableFooter->addRow();
                        $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('(___________________________________)', null, $cellHCentered);
                        $row->addCell(4000, null)->addText($this->getEmployeeName($department->id), null, $cellHCentered);
                        $row->addCell(4000, null)->addText($this->getEmployeeName($teacher->id), null, $cellHCentered);
                        $row = $tableFooter->addRow();
                        $row->addCell(2000, array('vMerge' => 'continue'));
                        $row->addCell(4000, null)->addText('NIP: ' . $department->employee_id, null, $cellHCentered);
                        $row->addCell(4000, null)->addText('NIP: ' . $teacher->employee_id, null, $cellHCentered);
                    }
                }
            }

            if ($request->lesson == "true")
            {
                $section->addPageBreak();

                $tableHead = $section->addTable($tableStyle);
                $tableHead->addRow();

                $cell3 = $tableHead->addCell(10000, $cellColSpan);
                $textrun3 = $cell3->addTextRun($cellHCentered);
                $textrun3->addText('Presensi Pelajaran', $subTitleStyle);

                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Departemen', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->department, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Tahun Ajaran', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->schoolyear, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Semester', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->semester, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Tingkat/Kelas', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $request->grade .' - '. $request->class, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('NIS', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $student->student_no, null, $cellHLeft);
                $tableHead->addRow();
                $tableHead->addCell(2000, $cellVCentered)->addText('Nama', null, $cellHLeft);
                $tableHead->addCell(6000, $cellVCentered)->addText(': ' . $student->name, null, $cellHLeft);

                $section->addTextBreak(2, null, null);

                $tableLesson = $section->addTable($tableStyle);
                $row = $tableLesson->addRow();
                $row->addCell(3000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Pelajaran', $subTitleStyle, $cellHCentered);
                $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Hadir', $subTitleStyle, $cellHCentered);
                $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Sakit', $subTitleStyle, $cellHCentered);
                $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Ijin', $subTitleStyle, $cellHCentered);
                $row->addCell(2000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Alpa', $subTitleStyle, $cellHCentered);

                $row = $tableLesson->addRow();
                $row->addCell(3000, array('vMerge' => 'continue'));
                $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);
                $row->addCell(1500, $cellVCentered)->addText('Jumlah', $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText('%', $subTitleStyle, $cellHCentered);

                $present = [];
                $sick = [];
                $permit = [];
                $absent = [];
                foreach ($lessons as $lesson)
                {
                    if ($lesson->student_id == $student->id)
                    {
                        $row = $tableLesson->addRow();
                        $row->addCell(3000, $cellVCentered)->addText(strtoupper($lesson->lesson), $subTitleStyle, $cellHLeft);
                        $counter = 1;
                        $presences_lesson = $this->getPresenceLessons($student->id, $request->class_id, $request->semester_id, $request->period_start, $request->period_end);
                        foreach ($presences_lesson as $presence)
                        {
                            if ($presence->lesson_id == $lesson->lesson_id)
                            {
                                $present[$counter] = $presence->present;
                                $sick[$counter] = $presence->sick;
                                $permit[$counter] = $presence->permit;
                                $absent[$counter] = $presence->absent;

                                $row->addCell(1500, $cellVCentered)->addText($presence->present, $subTitleStyle, $cellHCentered);
                                $row->addCell(500, $cellVCentered)->addText($presence->present != 0 && $presence->total != 0 ? round(($presence->present / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                                $row->addCell(1500, $cellVCentered)->addText($presence->sick, $subTitleStyle, $cellHCentered);
                                $row->addCell(500, $cellVCentered)->addText($presence->sick != 0 && $presence->total != 0 ? round(($presence->sick / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                                $row->addCell(1500, $cellVCentered)->addText($presence->permit, $subTitleStyle, $cellHCentered);
                                $row->addCell(500, $cellVCentered)->addText($presence->permit != 0 && $presence->total != 0 ? round(($presence->permit / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                                $row->addCell(1500, $cellVCentered)->addText($presence->absent, $subTitleStyle, $cellHCentered);
                                $row->addCell(500, $cellVCentered)->addText($presence->absent != 0 && $presence->total != 0 ? round(($presence->absent / $presence->total) * 100,2) : 0, $subTitleStyle, $cellHCentered);
                            }
                            $counter++;
                        }
                    }
                }
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
                $row = $tableLesson->addRow();
                $row->addCell(3000, $cellVCentered)->addText('Total', $subTitleStyle, $cellHCentered);
                $row->addCell(1500, $cellVCentered)->addText($prs, $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText(null);
                $row->addCell(1500, $cellVCentered)->addText($sck, $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText(null);
                $row->addCell(1500, $cellVCentered)->addText($lve, $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText(null);
                $row->addCell(1500, $cellVCentered)->addText($abs, $subTitleStyle, $cellHCentered);
                $row->addCell(500, $cellVCentered)->addText(null);

                $section->addTextBreak(2, null, null);

                $tableFooter = $section->addTable($tableStyleHeader);
                $row = $tableFooter->addRow();
                $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('Orang Tua/Wali Santri', $subTitleStyle, $cellHCentered);
                $row->addCell(8000, array('gridSpan' => 2, 'vMerge' => 'restart'))->addText('Mengetahui,', $subTitleStyle, $cellHCentered);
                $row = $tableFooter->addRow();
                $row->addCell(2000, array('vMerge' => 'continue'));
                $row->addCell(4000, $cellVCentered)->addText('Kepala Sekolah', $subTitleStyle, $cellHCentered);
                $row->addCell(4000, $cellVCentered)->addText('Wali Kelas', $subTitleStyle, $cellHCentered);
                $row = $tableFooter->addRow();
                $row->addCell(2000, null)->addTextBreak(2, null, null);
                $row->addCell(4000, null);
                $row->addCell(4000, null);
                $row = $tableFooter->addRow();
                $row->addCell(2000, array('vMerge' => 'restart', 'valign' => 'center'))->addText('(___________________________________)', null, $cellHCentered);
                $row->addCell(4000, null)->addText($this->getEmployeeName($department->id), null, $cellHCentered);
                $row->addCell(4000, null)->addText($this->getEmployeeName($teacher->id), null, $cellHCentered);
                $row = $tableFooter->addRow();
                $row->addCell(2000, array('vMerge' => 'continue'));
                $row->addCell(4000, null)->addText('NIP: ' . $department->employee_id, null, $cellHCentered);
                $row->addCell(4000, null)->addText('NIP: ' . $teacher->employee_id, null, $cellHCentered);
            }

            $section->addPageBreak();
        }

        $filename = date('Ymdhis') . '_' . config('app.name') . '_rapor_kelas_' . strtolower($request->class) . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('storage/downloads/'.$filename);
        echo $filename;
    }

    // helpers
    private function getAspects($student_id, $class_id, $semester_id)
    {
        return DB::select('SELECT DISTINCT d.id, d.basis, d.remark, b.student_id
                    FROM academic.exam_report_score_infos a, academic.exam_report_score_finals b, academic.lesson_assessments c, academic.score_aspects d
                    WHERE a.id = b.exam_report_info_id AND b.student_id = ? AND a.semester_id = ? AND a.class_id = ? AND b.lesson_assessment_id = c.id AND c.score_aspect_id = d.id',
                    [$student_id, $semester_id, $class_id]
                );
    }

    private function getLessonScores($student_id, $class_id, $semester_id)
    {
        return DB::select('SELECT a.id, a.name, a.group_id, e.group, c.student_id
                    FROM academic.lessons a, academic.exams b, academic.exam_scores c, academic.students d, academic.lesson_groups e
                    WHERE b.id = c.exam_id AND c.student_id = d.id AND b.lesson_id = a.id AND a.group_id = e.id AND b.semester_id = ? AND b.class_id = ? AND d.id = ?
                    GROUP BY a.id, a.name, a.group_id, e.group, c.student_id',
                    [$semester_id, $class_id, $student_id]
                );
    }

    private function getValues($student_id, $lesson_id, $semester_id, $class_id, $aspect)
    {
        return DB::select('SELECT a.value, a.value_letter, a.comment
                    FROM academic.exam_report_score_finals a, academic.exam_report_score_infos b, academic.lesson_assessments c
                    WHERE b.id = a.exam_report_info_id AND a.student_id = ? AND b.lesson_id = ? AND b.semester_id = ? AND b.class_id = ? AND a.lesson_assessment_id = c.id AND c.score_aspect_id = ?',
                    [$student_id, $lesson_id, $semester_id, $class_id, $aspect]
                );
    }

    private function getExams($student_id, $class_id, $semester_id)
    {
        if ($student_id > 0)
        {
            return ExamReportScoreFinal::where('student_id', $student_id)
                            ->whereHas('getExamReport', function ($query) use ($class_id, $semester_id) {
                                $query = $query->where('class_id', $class_id);
                                $query = $query->where('semester_id', $semester_id);
                            })
                            ->get()->map(function ($model) use ($student_id) {
                                $model['remark'] = optional($model->getScoreAspect)->remark;
                                $model['lesson_id'] = $model->getExamReport->lesson_id;
                                $model['score_aspect_id'] = $model->getExamReport->score_aspect_id;
                                $model['lesson'] = optional($model->getLesson)->name;
                                $model['lesson_group'] = optional(optional($model->getLesson)->getLessonGroup)->group;
                                $model['value_min'] = 0;
                                return $model;
                            });
        } else {
            return ExamReportScoreFinal::whereHas('getExamReport', function ($query) use ($class_id, $semester_id) {
                                $query = $query->where('class_id', $class_id);
                                $query = $query->where('semester_id', $semester_id);
                            })
                            ->get()->map(function ($model) use ($student_id) {
                                $model['remark'] = optional($model->getScoreAspect)->remark;
                                $model['lesson_id'] = optional($model->getExamReport)->lesson_id;
                                $model['score_aspect_id'] = optional($model->getExamReport)->score_aspect_id;
                                $model['lesson'] = optional($model->getLesson)->name;
                                $model['lesson_group'] = optional(optional($model->getLesson)->getLessonGroup)->group;
                                $model['value_min'] = 0;
                                return $model;
                            });
        }
    }

    private function getLessons($student_id, $class_id, $semester_id)
    {
        if ($student_id > 0)
        {
            return ExamReportScoreFinal::select('academic.exam_reports.lesson_id',DB::raw('academic.lessons.name AS lesson'),DB::raw('academic.lesson_groups.group AS lesson_group'),DB::raw('academic.exam_report_score_infos.value as value_min'),'academic.lessons.group_id')
                    ->join('academic.exam_reports', 'academic.exam_reports.id','=','academic.exam_report_score_finals.exam_report_id')
                    ->join('academic.lessons', 'academic.lessons.id','=','academic.exam_reports.lesson_id')
                    ->join('academic.lesson_groups', 'academic.lesson_groups.id','=','academic.lessons.group_id')
                    ->join('academic.exam_report_score_infos', 'academic.exam_report_score_infos.id','=','academic.exam_report_score_finals.exam_report_info_id')
                    ->where('academic.exam_report_score_finals.student_id', $student_id)
                    ->where('academic.exam_reports.class_id', $class_id)
                    ->where('academic.exam_reports.semester_id', $semester_id)
                    ->groupBy('academic.exam_reports.lesson_id','academic.lessons.name','academic.lesson_groups.group','academic.exam_report_score_infos.value','academic.lessons.group_id')
                    ->get();
        } else {
            return ExamReportScoreFinal::select('academic.exam_reports.lesson_id','academic.exam_report_score_finals.student_id',DB::raw('academic.lessons.name AS lesson'),DB::raw('academic.lesson_groups.group AS lesson_group'),DB::raw('academic.exam_report_score_infos.value as value_min'),'academic.lessons.group_id')
                    ->join('academic.exam_reports', 'academic.exam_reports.id','=','academic.exam_report_score_finals.exam_report_id')
                    ->join('academic.lessons', 'academic.lessons.id','=','academic.exam_reports.lesson_id')
                    ->join('academic.lesson_groups', 'academic.lesson_groups.id','=','academic.lessons.group_id')
                    ->join('academic.exam_report_score_infos', 'academic.exam_report_score_infos.id','=','academic.exam_report_score_finals.exam_report_info_id')
                    ->where('academic.exam_reports.class_id', $class_id)
                    ->where('academic.exam_reports.semester_id', $semester_id)
                    ->groupBy('academic.exam_reports.lesson_id','academic.exam_report_score_finals.student_id','academic.lessons.name','academic.lesson_groups.group','academic.exam_report_score_infos.value','academic.lessons.group_id')
                    ->get();
        }
    }

    private function getPresenceDaily($student_id, $class_id, $semester_id, $period_start, $period_end)
    {
        if ($student_id > 0)
        {
            return PresenceDaily::select(
                        DB::raw('SUM(academic.presence_daily_students.present) as present'),
                        DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                        DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                        DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                        DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                        DB::raw('SUM(academic.presence_daily_students.present+academic.presence_daily_students.permit+academic.presence_daily_students.sick+academic.presence_daily_students.absent+academic.presence_daily_students.leave) as total'),
                    )
                    ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                    ->where('academic.presence_dailies.class_id', $class_id)
                    ->where('academic.presence_dailies.semester_id', $semester_id)
                    ->whereDate('academic.presence_dailies.start_date','>=', $this->formatDate($period_start,'sys'))
                    ->whereDate('academic.presence_dailies.end_date','<=', $this->formatDate($period_end,'sys'))
                    ->where('academic.presence_daily_students.student_id', $student_id)
                    ->first();
        } else {
            return PresenceDaily::select(
                        'academic.presence_daily_students.student_id',
                        DB::raw('SUM(academic.presence_daily_students.present) as present'),
                        DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                        DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                        DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                        DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                        DB::raw('SUM(academic.presence_daily_students.present+academic.presence_daily_students.permit+academic.presence_daily_students.sick+academic.presence_daily_students.absent+academic.presence_daily_students.leave) as total'),
                    )
                    ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                    ->where('academic.presence_dailies.class_id', $class_id)
                    ->where('academic.presence_dailies.semester_id', $semester_id)
                    ->whereDate('academic.presence_dailies.start_date','>=', $this->formatDate($period_start,'sys'))
                    ->whereDate('academic.presence_dailies.end_date','<=', $this->formatDate($period_end,'sys'))
                    ->groupBy('academic.presence_daily_students.student_id')
                    ->get();
        }

    }

    private function getPresenceLessons($student_id, $class_id, $semester_id, $period_start, $period_end)
    {
        if ($student_id > 0)
        {
            return PresenceLesson::select(
                        'academic.presence_lessons.lesson_id',
                        DB::raw('COUNT(presence) AS total'),
                        DB::raw('SUM( CASE WHEN presence = 0 THEN 1 ELSE 0 END ) AS present'),
                        DB::raw('SUM( CASE WHEN presence = 1 THEN 1 ELSE 0 END ) AS permit'),
                        DB::raw('SUM( CASE WHEN presence = 2 THEN 1 ELSE 0 END ) AS sick'),
                        DB::raw('SUM( CASE WHEN presence = 3 THEN 1 ELSE 0 END ) AS leave'),
                        DB::raw('SUM( CASE WHEN presence = 4 THEN 1 ELSE 0 END ) AS absent'),
                    )
                    ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                    ->where('academic.presence_lessons.class_id', $class_id)
                    ->where('academic.presence_lessons.semester_id', $semester_id)
                    ->whereDate('academic.presence_lessons.date','>=', $this->formatDate($period_start,'sys'))
                    ->whereDate('academic.presence_lessons.date','<=', $this->formatDate($period_end,'sys'))
                    ->where('academic.presence_lesson_students.student_id', $student_id)
                    ->groupBy('academic.presence_lessons.lesson_id')
                    ->get();
        } else {
            return PresenceLesson::select(
                        'academic.presence_lessons.lesson_id',
                        DB::raw('COUNT(presence) AS total'),
                        DB::raw('SUM( CASE WHEN presence = 0 THEN 1 ELSE 0 END ) AS present'),
                        DB::raw('SUM( CASE WHEN presence = 1 THEN 1 ELSE 0 END ) AS permit'),
                        DB::raw('SUM( CASE WHEN presence = 2 THEN 1 ELSE 0 END ) AS sick'),
                        DB::raw('SUM( CASE WHEN presence = 3 THEN 1 ELSE 0 END ) AS leave'),
                        DB::raw('SUM( CASE WHEN presence = 4 THEN 1 ELSE 0 END ) AS absent'),
                    )
                    ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                    ->where('academic.presence_lessons.class_id', $class_id)
                    ->where('academic.presence_lessons.semester_id', $semester_id)
                    ->whereDate('academic.presence_lessons.date','>=', $this->formatDate($period_start,'sys'))
                    ->whereDate('academic.presence_lessons.date','<=', $this->formatDate($period_end,'sys'))
                    ->groupBy('academic.presence_lessons.lesson_id','academic.presence_lesson_students.student_id')
                    ->get();
        }
    }
}
