<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\ExamReportComment;
use Modules\Academic\Entities\ExamReportCommentLesson;
use Modules\Academic\Entities\ExamReportCommentSocial;
use Carbon\Carbon;

class ExamReportCommentEloquent implements ExamReportCommentRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

    public function upsert(Request $request, $subject)
    {
        return ExamReportComment::upsert([
                [
                    'student_id' => $request->student_id,
                    'class_id' => $request->class_id,
                    'semester_id' => $request->semester_id,
                    'type_id' => $request->type_id[0],
                    'aspect' => 'spiritual',
                    'comment' => $request->comment[0],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'student_id' => $request->student_id,
                    'class_id' => $request->class_id,
                    'semester_id' => $request->semester_id,
                    'type_id' => $request->type_id[1],
                    'aspect' => 'social',
                    'comment' => $request->comment[1],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            ],['student_id','class_id','semester_id','aspect'],['comment','type_id','updated_at']);
    }

    public function destroy($request, $subject)
    {
        $this->logActivity($request, 0, $subject, 'Hapus', '');
        return ExamReportComment::where('student_id', $request->student_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->delete();
    }

	public function createLesson(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah', 'lesson');
		return ExamReportCommentLesson::create($payload);
	}

	public function updateLesson(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['type_id','type','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah', 'lesson');
        // 
        return ExamReportCommentLesson::where('id', $payload['id'])->update($payload);
	}

    public function destroyLesson($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus', 'lesson');
        return ExamReportCommentLesson::destroy($id);
    }

    public function createSocial(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah', 'social');
        return ExamReportCommentSocial::create($payload);
    }

    public function updateSocial(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah', 'social');
        // 
        return ExamReportCommentSocial::where('id', $payload['id'])->update($payload);
    }

    public function destroySocial($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus', 'social');
        return ExamReportCommentSocial::destroy($id);
    }

	private function logActivity(Request $request, $model_id, $subject, $action, $option) 
	{
		if ($action == 'Tambah')
		{
            switch ($option) 
            {
                case 'lesson':
                    $data = array(
                        'lesson_id' => $request->lesson_id, 
                        'score_aspect_id' => $request->score_aspect_id, 
                        'grade_id' => $request->grade_id, 
                        'comment' => $request->comment, 
                        'is_active' => $request->is_active, 
                    );
                    break;
                case 'social':
                    $data = array(
                        'lesson_id' => $request->lesson_id, 
                        'type_id' => $request->type_id, 
                        'grade_id' => $request->grade_id, 
                        'aspect' => $request->aspect, 
                        'comment' => $request->comment, 
                        'is_active' => $request->is_active, 
                    );
                    break;
                default:
                    // code...
                    break;
            }
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
            switch ($option) 
            {
                case 'lesson':
                    $query = ExamReportCommentLesson::find($model_id);
                    $before = array(
                        'lesson_id' => $query->lesson_id, 
                        'score_aspect_id' => $query->score_aspect_id, 
                        'grade_id' => $query->grade_id, 
                        'comment' => $query->comment, 
                        'is_active' => $query->is_active, 
                    );
                    $after = array(
                        'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id, 
                        'score_aspect_id' => $request->has('score_aspect_id') ? $request->score_aspect_id : $query->score_aspect_id, 
                        'grade_id' => $request->has('grade_id') ? $request->grade_id : $query->grade_id, 
                        'comment' => $request->has('comment') ? $request->comment : $query->comment, 
                        'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active, 
                    );
                    break;
                case 'social':
                    $query = ExamReportCommentSocial::find($model_id);
                    $before = array(
                        'lesson_id' => $query->lesson_id, 
                        'type_id' => $query->type_id, 
                        'grade_id' => $query->grade_id, 
                        'aspect' => $query->aspect, 
                        'comment' => $query->comment, 
                        'is_active' => $query->is_active, 
                    );
                    $after = array(
                        'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id, 
                        'type_id' => $request->has('type_id') ? $request->type_id : $query->type_id, 
                        'grade_id' => $request->has('grade_id') ? $request->grade_id : $query->grade_id, 
                        'aspect' => $request->has('aspect') ? $request->aspect : $query->aspect, 
                        'comment' => $request->has('comment') ? $request->comment : $query->comment, 
                        'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active, 
                    );
                    break;
                default:
                    $query = ExamReportComment::where('student_id', $request->student_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->first();
                    $before = array(
                        'student_id' => optional($query)->student_id, 
                        'class_id' => optional($query)->class_id, 
                        'semester_id' => optional($query)->semester_id, 
                        'aspect' => optional($query)->aspect, 
                        'type_id' => optional($query)->type_id, 
                    );
                    $after = array(
                        'student_id' => $request->has('student_id') ? $request->student_id : optional($query)->student_id, 
                        'class_id' => $request->has('class_id') ? $request->class_id : optional($query)->class_id, 
                        'semester_id' => $request->has('semester_id') ? $request->semester_id : optional($query)->semester_id, 
                        'aspect' => $request->has('aspect') ? $request->aspect : optional($query)->aspect, 
                        'type_id' => $request->has('type_id') ? $request->type_id : optional($query)->type_id, 
                    );
                    break;
            }
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		} 
	}
}