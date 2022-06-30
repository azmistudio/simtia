<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReportComment extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_report_comments';

    protected $fillable = [
        'student_id',
        'class_id',
        'semester_id',
        'type_id',
        'aspect',
        'comment',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamReportCommentFactory::new();
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getSemester()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'id', 'semester_id');
    }

    public function getType()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'type_id');
    }
}
