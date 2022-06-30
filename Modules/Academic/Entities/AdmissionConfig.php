<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdmissionConfig extends Model
{
    use HasFactory;

    protected $table = 'academic.admission_configs';

    protected $fillable = [
        'admission_id',
        'donate_code_1',
        'donate_name_1',
        'donate_code_2',
        'donate_name_2',
        'exam_code_01',
        'exam_name_01',
        'exam_code_02',
        'exam_name_02',
        'exam_code_03',
        'exam_name_03',
        'exam_code_04',
        'exam_name_04',
        'exam_code_05',
        'exam_name_05',
        'exam_code_06',
        'exam_name_06',
        'exam_code_07',
        'exam_name_07',
        'exam_code_08',
        'exam_name_08',
        'exam_code_09',
        'exam_name_09',
        'exam_code_10',
        'exam_name_10',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AdmissionConfigFactory::new();
    }

    public function getAdmission()
    {
        return $this->hasOne('Modules\Academic\Entities\Admission', 'id', 'admission_id');
    }
}
