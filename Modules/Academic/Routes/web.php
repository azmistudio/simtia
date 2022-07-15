<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']], function() {
    Route::prefix('academic')->group(function() {
        Route::get('/', 'AcademicController@index')->middleware('permission:data_master-referensi_akademik-index');
        // grade
        Route::prefix('grade')->group(function() {
            Route::post('/data', 'AcademicController@dataGrade');
            Route::post('/store', 'AcademicController@storeGrade')->middleware('permission:data_master-referensi_akademik_tingkat-store');
            Route::get('/show/{id}', 'AcademicController@showGrade');
            Route::post('/destroy/{id}', 'AcademicController@destroyGrade')->middleware('permission:data_master-referensi_akademik_tingkat-destroy');
            Route::post('/export-pdf', 'AcademicController@toPdfGrade');
            Route::post('/combo-box/{id}', 'AcademicController@comboBoxGrade');
            Route::post('/combo-grid', 'AcademicController@comboGridGrade');
        });
        // schoolyear
        Route::prefix('school-year')->group(function() {
            Route::post('/data', 'AcademicController@dataSchoolYear');
            Route::post('/store', 'AcademicController@storeSchoolYear')->middleware('permission:data_master-referensi_akademik_tahunajar-store');
            Route::get('/show/{id}', 'AcademicController@showSchoolYear');
            Route::post('/destroy/{id}', 'AcademicController@destroySchoolYear')->middleware('permission:data_master-referensi_akademik_tahunajar-destroy');
            Route::post('/export-pdf', 'AcademicController@toPdfSchoolYear');
            Route::post('/combo-box/{id}', 'AcademicController@comboBoxSchoolYear');
            Route::post('/combo-grid', 'AcademicController@comboGridSchoolYear');
        });
        // semester
        Route::prefix('semester')->group(function() {
            Route::post('/data', 'AcademicController@dataSemester');
            Route::post('/store', 'AcademicController@storeSemester')->middleware('permission:data_master-referensi_akademik_semester-store');
            Route::get('/show/{id}', 'AcademicController@showSemester');
            Route::post('/destroy/{id}', 'AcademicController@destroySemester')->middleware('permission:data_master-referensi_akademik_semester-destroy');
            Route::post('/export-pdf', 'AcademicController@toPdfSemester');
            Route::post('/combo-box/{id}', 'AcademicController@comboBoxSemester');
            Route::post('/combo-grid', 'AcademicController@comboGridSemester');
        });
        // class
        Route::prefix('class')->group(function() {
            Route::post('/data', 'AcademicController@dataClass');
            Route::post('/store', 'AcademicController@storeClass')->middleware('permission:data_master-referensi_akademik_kelas-store');
            Route::get('/show/{id}', 'AcademicController@showClass');
            Route::post('/destroy/{id}', 'AcademicController@destroyClass')->middleware('permission:data_master-referensi_akademik_kelas-destroy');
            Route::post('/export-pdf', 'AcademicController@toPdfClass');
            Route::post('/combo-grid', 'AcademicController@comboGridClass');
            Route::post('/combo-grid/view', 'AcademicController@comboGridClassView');
            Route::post('/placement/combo-grid', 'AcademicController@comboGridClassPlacement');
            Route::post('/student/combo-grid', 'AcademicController@comboGridClassStudent');
            Route::post('/only/combo-grid', 'AcademicController@comboGridClassOnly');
        });
        // admission
        Route::prefix('admission')->group(function() {
            Route::get('/', 'AdmissionController@index')->middleware('permission:akademik-psb-index');
            Route::post('/data', 'AdmissionController@data');
            Route::post('/store', 'AdmissionController@store')->middleware('permission:akademik-psb-store');
            Route::get('/show/{id}', 'AdmissionController@show');
            Route::post('/destroy/{id}', 'AdmissionController@destroy')->middleware('permission:akademik-psb-destroy');
            Route::post('/export-pdf', 'AdmissionController@toPdf');
            Route::get('/combo-grid', 'AdmissionController@comboGrid');
            // prospective-group
            Route::prefix('prospective-group')->group(function() {
                Route::get('/', 'AdmissionProspectGroupController@index')->middleware('permission:akademik-psb_kelompok_calon_santri-index');
                Route::post('/data', 'AdmissionProspectGroupController@data');
                Route::post('/store', 'AdmissionProspectGroupController@store')->middleware('permission:akademik-psb_kelompok_calon_santri-store');
                Route::get('/show/{id}', 'AdmissionProspectGroupController@show');
                Route::post('/destroy/{id}', 'AdmissionProspectGroupController@destroy')->middleware('permission:akademik-psb_kelompok_calon_santri-destroy');
                Route::post('/export-pdf', 'AdmissionProspectGroupController@toPdf');
                Route::post('/combo-grid', 'AdmissionProspectGroupController@comboGrid');
            });
            // prospective-student
            Route::prefix('prospective-student')->group(function() {
                Route::get('/', 'AdmissionProspectController@index')->middleware('permission:akademik-psb_data_calon_santri-index');
                Route::post('/data', 'AdmissionProspectController@data');
                Route::post('/data/view', 'AdmissionProspectController@dataView');
                Route::post('/combo-grid', 'AdmissionProspectController@comboGrid');
                Route::post('/store', 'AdmissionProspectController@store')->middleware('permission:akademik-psb_data_calon_santri-store');
                Route::get('/show/{id}', 'AdmissionProspectController@show');
                Route::post('/destroy/{id}', 'AdmissionProspectController@destroy')->middleware('permission:akademik-psb_data_calon_santri-destroy');
                Route::post('/print', 'AdmissionProspectController@print');
                Route::post('/export-excel', 'AdmissionProspectController@toExcel');
                Route::post('/export-pdf', 'AdmissionProspectController@toPdf');
            });
            // placement
            Route::prefix('placement')->group(function() {
                Route::get('/', 'AdmissionPlacementController@index')->middleware('permission:akademik-psb_penempatan_santri_baru-index');
                Route::post('/data', 'AdmissionPlacementController@data');
                Route::post('/store', 'AdmissionPlacementController@store')->middleware('permission:akademik-psb_penempatan_santri_baru-store');
                Route::get('/show/{id}', 'AdmissionPlacementController@show');
                Route::post('/destroy', 'AdmissionPlacementController@destroy')->middleware('permission:akademik-psb_penempatan_santri_baru-destroy');
            });
            // column
            Route::prefix('column')->group(function() {
                Route::get('/', 'AcademicColumnController@index')->middleware('permission:akademik-psb_kolom_calon_siswa-index');
                Route::post('/data', 'AcademicColumnController@data');
                Route::post('/store', 'AcademicColumnController@store')->middleware('permission:akademik-psb_kolom_calon_siswa-store');
                Route::get('/show/{id}', 'AcademicColumnController@show');
                Route::post('/destroy/{id}', 'AcademicColumnController@destroy')->middleware('permission:akademik-psb_kolom_calon_siswa-destroy');
                Route::get('/view/{id}', 'AcademicColumnController@view');
                // option
                Route::prefix('option')->group(function() {
                    Route::get('/', 'AcademicColumnOptionController@index');
                    Route::post('/data/{id}', 'AcademicColumnOptionController@data');
                    Route::post('/store/{id}', 'AcademicColumnOptionController@store')->middleware('permission:akademik-psb_kolom_pilihan_calon_siswa-store');
                    Route::get('/show/{id}', 'AcademicColumnOptionController@show');
                    Route::post('/destroy', 'AcademicColumnOptionController@destroy')->middleware('permission:akademik-psb_kolom_pilihan_calon_siswa-destroy');
                    Route::get('/data/list/{id}', 'AcademicColumnOptionController@dataList');
                });
            });
            // config
            Route::prefix('config')->group(function() {
                Route::get('/', 'AdmissionConfigController@index')->middleware('permission:akademik-psb_konfigurasi-index');
                Route::post('/data', 'AdmissionConfigController@data');
                Route::post('/store', 'AdmissionConfigController@store')->middleware('permission:akademik-psb_konfigurasi-store');
                Route::get('/show/{id}', 'AdmissionConfigController@show');
                Route::get('/getbyadmission/{id}', 'AdmissionConfigController@getByAdmission');
                Route::post('/destroy/{id}', 'AdmissionConfigController@destroy')->middleware('permission:akademik-psb_konfigurasi-destroy');
            });
        });

        // room
        Route::prefix('room')->group(function() {

            // placement
            Route::prefix('placement')->group(function() {
                Route::get('/', 'RoomPlacementController@index')->middleware('permission:akademik-penempatan_kamar_santri-index');
                Route::post('/data', 'RoomPlacementController@data');
                Route::post('/store', 'RoomPlacementController@store')->middleware('permission:akademik-penempatan_kamar_santri-store');
                Route::post('/destroy', 'RoomPlacementController@destroy')->middleware('permission:akademik-penempatan_kamar_santri-destroy');
            });
        });
    
        // lesson
        Route::prefix('lesson')->group(function() {
            // reference
            Route::prefix('reference')->group(function() {
                Route::get('/', 'LessonController@index')->middleware('permission:akademik-referensi_pelajaran-index');
                // score aspect
                Route::prefix('score-aspect')->group(function() {
                    Route::post('/data', 'LessonController@dataScoreAspect');
                    Route::post('/store', 'LessonController@storeScoreAspect')->middleware('permission:akademik-referensi_pelajaran_aspek_penilaian-store');
                    Route::get('/show/{id}', 'LessonController@showScoreAspect');
                    Route::post('/destroy/{id}', 'LessonController@destroyScoreAspect')->middleware('permission:akademik-referensi_pelajaran_aspek_penilaian-destroy');
                    Route::post('/export-pdf', 'LessonController@toPdfScoreAspect');
                });
                // lesson group
                Route::prefix('lesson-group')->group(function() {
                    Route::post('/data', 'LessonController@dataLessonGroup');
                    Route::post('/store', 'LessonController@storeLessonGroup')->middleware('permission:akademik-referensi_pelajaran_kelompok_pelajaran-store');
                    Route::get('/show/{id}', 'LessonController@showLessonGroup');
                    Route::post('/destroy/{id}', 'LessonController@destroyLessonGroup')->middleware('permission:akademik-referensi_pelajaran_kelompok_pelajaran-destroy');
                    Route::post('/export-pdf', 'LessonController@toPdfLessonGroup');
                });
            });
            // lesson data
            Route::get('/', 'LessonDataController@index')->middleware('permission:akademik-data_pelajaran-index');
            Route::post('/data', 'LessonDataController@data');
            Route::post('/store', 'LessonDataController@store')->middleware('permission:akademik-data_pelajaran-store');
            Route::get('/show/{id}', 'LessonDataController@show');
            Route::post('/destroy/{id}', 'LessonDataController@destroy')->middleware('permission:akademik-data_pelajaran-destroy');
            Route::post('/export-pdf', 'LessonDataController@toPdf');
            Route::post('/combo-grid', 'LessonDataController@comboGrid');
            Route::post('/teacher/combo-grid', 'LessonDataController@comboGridTeacher');
            Route::post('/combo-box/{id}', 'LessonDataController@comboBox');
            // lesson plan
            Route::get('/plan', 'LessonPlanController@index')->middleware('permission:akademik-rpp_pelajaran-index');
            Route::post('/plan/data', 'LessonPlanController@data');
            Route::post('/plan/store', 'LessonPlanController@store')->middleware('permission:akademik-rpp_pelajaran-store');
            Route::get('/plan/show/{id}', 'LessonPlanController@show');
            Route::post('/plan/destroy/{id}', 'LessonPlanController@destroy')->middleware('permission:akademik-rpp_pelajaran-destroy');
            Route::post('/plan/export-pdf', 'LessonPlanController@toPdf');
            Route::post('/plan/combo-box', 'LessonPlanController@comboBox');
            // lesson exam
            Route::get('/exam', 'LessonExamTypeController@index')->middleware('permission:akademik-jenis_pengujian-index');
            Route::post('/exam/data', 'LessonExamTypeController@data');
            Route::post('/exam/store', 'LessonExamTypeController@store')->middleware('permission:akademik-jenis_pengujian-store');
            Route::get('/exam/show/{id}', 'LessonExamTypeController@show');
            Route::post('/exam/destroy/{id}', 'LessonExamTypeController@destroy')->middleware('permission:akademik-jenis_pengujian-destroy');
            Route::post('/exam/export-pdf', 'LessonExamTypeController@toPdf');
            Route::get('/exam/list/{id}/{aspect_id}', 'LessonExamTypeController@list');
            Route::post('/exam/combo-box/{id}', 'LessonExamTypeController@comboBox');
            // lesson grading
            Route::prefix('grading')->group(function() {
                Route::get('/', 'LessonGradingController@index')->middleware('permission:akademik-aturan_grading-index');
                Route::post('/data', 'LessonGradingController@data');
                Route::post('/store', 'LessonGradingController@store')->middleware('permission:akademik-aturan_grading-store');
                Route::get('/show/{employee_id}/{grade_id}/{lesson_id}/{score_aspect_id}', 'LessonGradingController@show');
                Route::post('/destroy/{employee_id}/{grade_id}/{lesson_id}/{score_aspect_id}', 'LessonGradingController@destroy')->middleware('permission:akademik-aturan_grading-destroy');
                Route::post('/export-pdf', 'LessonGradingController@toPdf');
                Route::post('/combo-box/{employee_id}/{grade_id}/{lesson_id}', 'LessonGradingController@combobox');
                Route::get('/combo-box/grade', 'LessonGradingController@comboboxGrade');
            });
            // lesson assessment
            Route::prefix('assessment')->group(function() {
                Route::get('/', 'LessonAssessmentController@index')->middleware('permission:akademik-aturan_penilaian_rapor-index');
                Route::post('/data', 'LessonAssessmentController@data');
                Route::post('/store', 'LessonAssessmentController@store')->middleware('permission:akademik-aturan_penilaian_rapor-store');
                Route::get('/show/{employee_id}/{grade_id}/{lesson_id}/{score_aspect_id}', 'LessonAssessmentController@show');
                Route::post('/destroy/{employee_id}/{grade_id}/{lesson_id}/{score_aspect_id}', 'LessonAssessmentController@destroy')->middleware('permission:akademik-aturan_penilaian_rapor-destroy');
                Route::post('/export-pdf', 'LessonAssessmentController@toPdf');
                Route::post('/combo-box', 'LessonAssessmentController@combobox');
            });
            // lesson schedule
            Route::prefix('schedule')->group(function() {
                // time
                Route::prefix('time')->group(function() {
                    Route::get('/', 'LessonTimeController@index')->middleware('permission:akademik-jam_belajar-index');
                    Route::post('/data', 'LessonTimeController@data');
                    Route::post('/store', 'LessonTimeController@store')->middleware('permission:akademik-jam_belajar-store');
                    Route::get('/show/{id}', 'LessonTimeController@show');
                    Route::post('/destroy/{id}', 'LessonTimeController@destroy')->middleware('permission:akademik-jam_belajar-destroy');
                    Route::post('/export-pdf', 'LessonTimeController@toPdf');
                    Route::post('/combo-box/{id}', 'LessonTimeController@combobox');
                });
                // info
                Route::prefix('info')->group(function() {
                    Route::get('/', 'LessonScheduleInfoController@index');
                    Route::post('/list/{id}', 'LessonScheduleInfoController@list');
                    Route::post('/data/{id}', 'LessonScheduleInfoController@data');
                    Route::post('/store/{id}', 'LessonScheduleInfoController@store');
                    Route::post('/destroy', 'LessonScheduleInfoController@destroy');
                    Route::post('/combo-grid', 'LessonScheduleInfoController@comboGrid');
                });
                // teaching
                Route::prefix('teaching')->group(function() {
                    Route::get('/', 'LessonTeachingController@index')->middleware('permission:akademik-jadwal_guru-index');
                    Route::get('/teacher/{deptid}/{id}', 'LessonTeachingController@viewTeacher');
                    Route::get('/class/{deptid}', 'LessonTeachingController@viewClass');
                    Route::post('/data', 'LessonTeachingController@data');
                    Route::post('/store', 'LessonTeachingController@store')->middleware('permission:akademik-jadwal_guru-store');
                    Route::get('/show/{id}', 'LessonTeachingController@show');
                    Route::post('/destroy/{id}', 'LessonTeachingController@destroy')->middleware('permission:akademik-jadwal_guru-destroy');
                    Route::post('/print/{opt}', 'LessonTeachingController@print');
                    Route::post('/check', 'LessonTeachingController@checkExist');
                    Route::post('/combo-grid', 'LessonTeachingController@comboGrid');
                    Route::post('/combo-box/{seq}', 'LessonTeachingController@comboBox');
                    Route::post('/day/combo-box', 'LessonTeachingController@comboBoxDay');
                });
                // recap
                Route::prefix('recap')->group(function() {
                    Route::get('/', 'LessonTeachingController@indexRecap')->middleware('permission:akademik-rekapitulasi_jadwal_guru-index');
                    Route::post('/data', 'LessonTeachingController@dataRecap');
                    Route::post('/export-pdf', 'LessonTeachingController@toPdfRecap');
                });
            });
        });
        // teacher
        Route::prefix('teacher')->group(function() {
            Route::get('/', 'TeacherController@index')->middleware('permission:akademik-guru-index');
            Route::post('/data', 'TeacherController@data');
            Route::post('/store', 'TeacherController@store')->middleware('permission:akademik-guru-store');
            Route::get('/show/{id}', 'TeacherController@show');
            Route::post('/destroy/{id}', 'TeacherController@destroy')->middleware('permission:akademik-guru-destroy');
            Route::post('/export-pdf', 'TeacherController@toPdf');
            Route::post('/combo-grid', 'TeacherController@comboGrid');
            Route::post('/combo-grid/group', 'TeacherController@comboGridGroup');
            Route::post('/list/{id}/{deptid}', 'TeacherController@list');
        });
        // calendar
        Route::prefix('calendar')->group(function() {
            Route::get('/', 'CalendarController@index')->middleware('permission:akademik-kalender_akademik-index');
            Route::post('/data', 'CalendarController@data');
            Route::post('/activity/data', 'CalendarController@dataActivity');
            Route::post('/store/{id}', 'CalendarController@store')->middleware('permission:akademik-kalender_akademik-store');
            Route::post('/list', 'CalendarController@list');
            Route::post('/activity/store', 'CalendarController@storeActivity')->middleware('permission:akademik-kalender_akademik_aktivitas-store');
            Route::get('/show/{id}', 'CalendarController@show');
            Route::get('/activity/show/{id}', 'CalendarController@showActivity');
            Route::post('/destroy/{id}', 'CalendarController@destroy')->middleware('permission:akademik-kalender_akademik-destroy');
            Route::post('/activity/destroy/{id}', 'CalendarController@destroyActivity')->middleware('permission:akademik-kalender_akademik_aktivitas-destroy');
            Route::post('/export-pdf', 'CalendarController@toPdf');
            Route::get('/yearly/{id}', 'CalendarController@indexYearly');
        });
        // student
        Route::prefix('student')->group(function() {
            Route::get('/', 'StudentController@index')->middleware('permission:akademik-data_santri-index');
            Route::post('/data', 'StudentController@data');
            Route::post('/list', 'StudentController@list');
            Route::post('/store', 'StudentController@store')->middleware('permission:akademik-data_santri-store');
            Route::get('/show/{id}', 'StudentController@show');
            Route::post('/destroy/{id}', 'StudentController@destroy')->middleware('permission:akademik-data_santri-destroy');
            Route::post('/combo-grid', 'StudentController@comboGrid');
            Route::post('/data/room', 'StudentController@dataRoom');
            Route::post('/print', 'StudentController@print');
            Route::post('/export-excel', 'StudentController@toExcel');
            Route::post('/export-pdf', 'StudentController@toPdf');
            Route::post('/transfer/store/{id}', 'StudentController@storeTransfer');
            Route::post('/placement/combo-grid', 'StudentController@comboGridPlacement');
            
            // mutation
            Route::prefix('mutation')->group(function() {
                Route::get('/', 'StudentMutationController@index')->middleware('permission:akademik-mutasi_santri-index');
                Route::post('/data', 'StudentMutationController@data');
                Route::post('/store', 'StudentMutationController@store')->middleware('permission:akademik-mutasi_santri-store');
                Route::post('/destroy', 'StudentMutationController@destroy')->middleware('permission:akademik-mutasi_santri-destroy');
                Route::post('/combo-grid', 'StudentMutationController@combogrid');
                Route::post('/export-pdf', 'StudentMutationController@toPdf');
            });

            // memorize-card
            Route::prefix('memorize-card')->group(function() {
                Route::get('/', 'StudentMemorizeCardController@index')->middleware('permission:akademik-kartu_setoran_santri-index');
                Route::post('/data', 'StudentMemorizeCardController@data');
                Route::post('/store', 'StudentMemorizeCardController@store')->middleware('permission:akademik-kartu_setoran_santri-store');
                Route::get('/show/{class_id}/{date}', 'StudentMemorizeCardController@show');
                Route::post('/destroy', 'StudentMemorizeCardController@destroy')->middleware('permission:akademik-kartu_setoran_santri-destroy');
                Route::post('/data/card', 'StudentMemorizeCardController@dataCard');
                Route::post('/print', 'StudentMemorizeCardController@print');
                Route::post('/print/form', 'StudentMemorizeCardController@printForm');
            });

        });
        // presence
        Route::prefix('presence')->group(function() {
            // daily
            Route::prefix('daily')->group(function() {
                Route::get('/', 'PresenceController@index')->middleware('permission:akademik-presensi_harian-index');
                Route::post('/data', 'PresenceController@data');
                Route::post('/list', 'PresenceController@list');
                Route::post('/store', 'PresenceController@store')->middleware('permission:akademik-presensi_harian-store');
                Route::get('/show/{id}', 'PresenceController@show');
                Route::post('/destroy/{id}', 'PresenceController@destroy')->middleware('permission:akademik-presensi_harian-destroy');
                Route::post('/print', 'PresenceController@print');
                Route::post('/print/form', 'PresenceController@printForm');
            });
            // lesson
            Route::prefix('lesson')->group(function() {
                Route::get('/', 'PresenceController@indexLesson')->middleware('permission:akademik-presensi_pelajaran-index');
                Route::post('/data', 'PresenceController@dataLesson');
                Route::post('/list', 'PresenceController@listLesson');
                Route::post('/store', 'PresenceController@storeLesson')->middleware('permission:akademik-presensi_pelajaran-store');
                Route::get('/show/{id}', 'PresenceController@showLesson');
                Route::post('/destroy', 'PresenceController@destroyLesson')->middleware('permission:akademik-presensi_pelajaran-destroy');
                Route::post('/print/form', 'PresenceController@printFormLesson');
                Route::post('/combo-grid', 'PresenceController@comboGridLesson');
            });
        });
        // assessment
        Route::prefix('assessment')->group(function() {
            // lesson
            Route::prefix('lesson')->group(function() {
                Route::get('/', 'AssessmentLessonController@index')->middleware('permission:akademik-penilaian_pelajaran-index');
                Route::post('/data', 'AssessmentLessonController@data');
                Route::post('/list/{id}', 'AssessmentLessonController@list');
                Route::post('/store', 'AssessmentLessonController@store')->middleware('permission:akademik-penilaian_pelajaran-store');
                Route::get('/show/{id}', 'AssessmentLessonController@show');
                Route::post('/destroy/{id}', 'AssessmentLessonController@destroy')->middleware('permission:akademik-penilaian_pelajaran-destroy');
                Route::post('/export-pdf', 'AssessmentLessonController@toPdf');
                Route::post('/export-excel', 'AssessmentLessonController@toExcel');
                Route::post('/combo-grid', 'AssessmentLessonController@comboGrid');
                Route::post('/exam/combo-grid', 'AssessmentLessonController@comboGridExam');
                // score
                Route::prefix('score')->group(function() {
                    Route::get('/{id}/{height}', 'AssessmentLessonController@indexScore');
                    Route::post('/data', 'AssessmentLessonController@dataScore');
                    Route::post('/recalc', 'AssessmentLessonController@scoreRecalc');
                    Route::post('/data/weight', 'AssessmentLessonController@dataScoreWeight');
                    Route::post('/edit/store', 'AssessmentLessonController@storeScoreEdit');
                    Route::post('/final', 'AssessmentLessonController@storeFinal');
                });
                // dialog
                Route::prefix('dialog')->group(function() {
                    Route::get('/score/{id}', 'AssessmentLessonController@indexScoreDialog');
                    Route::get('/edit/score', 'AssessmentLessonController@indexScoreDialogEdit');
                });
                // form
                Route::prefix('form')->group(function() {
                    Route::post('/score/export-pdf', 'AssessmentLessonController@toPdfFormScore');
                    Route::post('/score/final/export-pdf', 'AssessmentLessonController@toPdfFormScoreFinal');
                    Route::post('/score/report/export-pdf', 'AssessmentLessonController@toPdfFormScoreReport');
                    Route::post('/report/comment/export-pdf', 'AssessmentLessonController@toPdfFormReportComment');
                });
            });
            // report
            Route::prefix('report')->group(function() {
                // formula
                Route::prefix('formula')->group(function() {
                    Route::get('/', 'AssessmentReportController@index')->middleware('permission:akademik-perhitungan_nilai_rapor-index');
                    Route::post('/data', 'AssessmentReportController@data');
                    Route::post('/list', 'AssessmentReportController@list');
                    Route::post('/store', 'AssessmentReportController@store')->middleware('permission:akademik-perhitungan_nilai_rapor-store');
                    Route::get('/show/{id}', 'AssessmentReportController@show');
                    Route::post('/destroy/{id}', 'AssessmentReportController@destroy')->middleware('permission:akademik-perhitungan_nilai_rapor-destroy');
                    Route::get('/load', 'AssessmentReportController@loadScore');
                    Route::post('/score/data', 'AssessmentReportController@dataScore');
                    Route::get('/info/show', 'AssessmentReportController@showInfoValue');
                    Route::post('/export-excel', 'AssessmentReportController@toExcel');
                    Route::post('/combo-grid', 'AssessmentReportController@comboGrid');
                });
                // comment
                Route::prefix('comment')->group(function() {
                    Route::get('/', 'AssessmentReportCommentController@index')->middleware('permission:akademik-komentar_rapor-index');
                    Route::post('/data', 'AssessmentReportCommentController@data');
                    Route::post('/store', 'AssessmentReportCommentController@store')->middleware('permission:akademik-komentar_rapor-store');
                    Route::post('/destroy', 'AssessmentReportCommentController@destroy')->middleware('permission:akademik-komentar_rapor-destroy');
                    Route::get('/lesson', 'AssessmentReportCommentController@indexLesson');
                    Route::get('/social', 'AssessmentReportCommentController@indexSocial');
                    Route::get('/view', 'AssessmentReportCommentController@indexView');
                    Route::get('/template', 'AssessmentReportCommentController@indexTemplate');
                    Route::post('/template/store', 'AssessmentReportCommentController@storeTemplate');
                    Route::post('/template/destroy', 'AssessmentReportCommentController@destroyTemplate');
                    Route::post('/template/combo-box', 'AssessmentReportCommentController@combobox');
                    Route::get('/template/combo-box/{id}', 'AssessmentReportCommentController@comboboxValue');
                });
                // score
                Route::prefix('score')->group(function() {
                    Route::get('/', 'AssessmentReportScoreController@index');
                    Route::get('/student', 'AssessmentReportScoreController@indexStudent');
                    Route::post('/export-word', 'AssessmentReportScoreController@toWord');
                    Route::post('/student/export-pdf', 'AssessmentReportScoreController@toPdfStudent');
                    Route::post('/student/export-word', 'AssessmentReportScoreController@toWordStudent');
                });
            });
            // score
            Route::prefix('score')->group(function() {
                Route::get('/audit', 'AssessmentScoreController@index')->middleware('permission:akademik-audit_perubahan_nilai-index');
                Route::post('/audit/data', 'AssessmentScoreController@data');
            });
        });

        // graduation
        Route::prefix('graduation')->group(function() {
            // promote
            Route::prefix('promote')->group(function() {
                Route::get('/', 'GraduationController@index')->middleware('permission:akademik-kenaikan_kelas-index');
                Route::post('/store', 'GraduationController@store')->middleware('permission:akademik-kenaikan_kelas-store');
                Route::post('/destroy', 'GraduationController@destroy')->middleware('permission:akademik-kenaikan_kelas-destroy');
            });
            // unpromote
            Route::prefix('unpromote')->group(function() {
                Route::get('/', 'GraduationController@indexUnpromote')->middleware('permission:akademik-tidak_naik_kelas-index');
                Route::post('/store', 'GraduationController@storeUnpromote')->middleware('permission:akademik-tidak_naik_kelas-store');
                Route::post('/destroy', 'GraduationController@destroyUnpromote')->middleware('permission:akademik-tidak_naik_kelas-destroy');
            });
            // mutation
            Route::prefix('mutation')->group(function() {
                Route::get('/', 'GraduationController@indexMutation')->middleware('permission:akademik-pindah_departemen-index');
                Route::post('/store', 'GraduationController@storeMutation')->middleware('permission:akademik-pindah_departemen-store');
                Route::post('/destroy', 'GraduationController@destroyMutation')->middleware('permission:akademik-pindah_departemen-destroy');
            });
            // alumni
            Route::prefix('alumni')->group(function() {
                Route::get('/', 'GraduationController@indexAlumni')->middleware('permission:akademik-kelulusan_alumni-index');
                Route::post('/store', 'GraduationController@storeAlumni')->middleware('permission:akademik-kelulusan_alumni-store');
                Route::post('/data', 'GraduationController@dataAlumni');
                Route::post('/combo-grid', 'GraduationController@combogridAlumni');
                Route::post('/destroy', 'GraduationController@destroyAlumni')->middleware('permission:akademik-kelulusan_alumni-destroy');
            });
        });
        // report
        Route::prefix('report')->group(function() {
            Route::get('/', 'ReportController@index');
            Route::post('/grade/combo-box/{id}/{is_all}', 'ReportController@comboBoxGrade');
            Route::post('/class/combo-box/{grade_id}/{schoolyear_id}/{is_all}', 'ReportController@comboBoxClass');
            Route::post('/lesson/combo-box/{deptid}/{is_all}', 'ReportController@comboBoxLesson');
            // admission
            Route::prefix('admission')->group(function() {
                // prospect
                Route::prefix('prospect')->group(function() {
                    Route::get('/', 'ReportController@admissionProspect');
                    Route::post('/data', 'ReportController@admissionProspectData');
                });

                // stat
                Route::prefix('stat')->group(function() {
                    Route::get('/', 'ReportController@admissionStat');
                    Route::post('/data', 'ReportController@admissionStatData');
                    Route::post('/data/detail', 'ReportController@admissionStatDataDetail');
                    Route::post('/print', 'ReportController@admissionStatPrint');
                });
            });
            // student
            Route::prefix('student')->group(function() {
                // stat
                Route::prefix('stat')->group(function() {
                    Route::get('/', 'ReportController@studentStat');
                    Route::post('/data', 'ReportController@studentStatData');
                    Route::post('/data/detail', 'ReportController@studentStatDataDetail');
                    Route::post('/print', 'ReportController@studentStatPrint');
                });
                // mutation
                Route::prefix('mutation')->group(function() {
                    // stat
                    Route::prefix('stat')->group(function() {
                        Route::get('/', 'ReportController@studentMutation');
                        Route::post('/data', 'ReportController@studentMutationData');
                        Route::post('/data/detail', 'ReportController@studentMutationDataDetail');
                        Route::post('/graph', 'ReportController@studentMutationGraph');
                        Route::post('/export-pdf', 'ReportController@studentMutationToPdf');
                    });
                });
            });
            // presence
            Route::prefix('presence')->group(function() {
                // daily
                Route::prefix('daily')->group(function() {
                    Route::get('/', 'ReportPresenceDailyController@presenceDaily');
                    Route::post('/data', 'ReportPresenceDailyController@presenceDailyData');
                    Route::post('/export-excel', 'ReportPresenceDailyController@presenceDailyToExcel');
                    Route::post('/export-pdf', 'ReportPresenceDailyController@presenceDailyToPdf');
                    // class
                    Route::prefix('class')->group(function() {
                        Route::get('/', 'ReportPresenceDailyController@presenceDailyClass');
                        Route::post('/data', 'ReportPresenceDailyController@presenceDailyClassData');
                        Route::post('/export-excel', 'ReportPresenceDailyController@presenceDailyClassToExcel');
                        Route::post('/export-pdf', 'ReportPresenceDailyController@presenceDailyClassToPdf');
                    });
                    // absent
                    Route::prefix('absent')->group(function() {
                        Route::get('/', 'ReportPresenceDailyController@presenceDailyAbsent');
                        Route::post('/data', 'ReportPresenceDailyController@presenceDailyAbsentData');
                        Route::post('/export-excel', 'ReportPresenceDailyController@presenceDailyAbsentToExcel');
                        Route::post('/export-pdf', 'ReportPresenceDailyController@presenceDailyAbsentToPdf');
                    });
                });
                // stat
                Route::prefix('stat')->group(function() {
                    Route::get('/', 'ReportPresenceDailyController@presenceStat');
                    Route::post('/data', 'ReportPresenceDailyController@presenceStatData');
                    Route::post('/export-pdf', 'ReportPresenceDailyController@presenceStatToPdf');
                    // class
                    Route::prefix('class')->group(function() {
                        Route::get('/', 'ReportPresenceDailyController@presenceStatClass');
                        Route::post('/data', 'ReportPresenceDailyController@presenceStatClassData');
                        Route::post('/export-pdf', 'ReportPresenceDailyController@presenceStatClassToPdf');
                    });
                });
                // lesson
                Route::prefix('lesson')->group(function() {
                    Route::get('/', 'ReportPresenceLessonController@presenceLesson');
                    Route::post('/data', 'ReportPresenceLessonController@presenceLessonData');
                    Route::post('/data/info', 'ReportPresenceLessonController@presenceLessonDataInfo');
                    Route::post('/export-excel', 'ReportPresenceLessonController@presenceLessonToExcel');
                    Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonToPdf');
                    // class
                    Route::prefix('class')->group(function() {
                        Route::get('/', 'ReportPresenceLessonController@presenceLessonClass');
                        Route::post('/data', 'ReportPresenceLessonController@presenceLessonClassData');
                        Route::post('/export-excel', 'ReportPresenceLessonController@presenceLessonClassToExcel');
                        Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonClassToPdf');
                    });
                    // teacher
                    Route::prefix('teacher')->group(function() {
                        Route::get('/', 'ReportPresenceLessonController@presenceLessonTeacher');
                        Route::post('/data', 'ReportPresenceLessonController@presenceLessonTeacherData');
                        Route::post('/export-excel', 'ReportPresenceLessonController@presenceLessonTeacherToExcel');
                        Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonTeacherToPdf');
                    });
                    // absent
                    Route::prefix('absent')->group(function() {
                        Route::get('/', 'ReportPresenceLessonController@presenceLessonAbsent');
                        Route::post('/data', 'ReportPresenceLessonController@presenceLessonAbsentData');
                        Route::post('/export-excel', 'ReportPresenceLessonController@presenceLessonAbsentToExcel');
                        Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonAbsentToPdf');
                    });
                    // reflection
                    Route::prefix('reflection')->group(function() {
                        Route::get('/', 'ReportPresenceLessonController@presenceLessonReflection');
                        Route::post('/data', 'ReportPresenceLessonController@presenceLessonReflectionData');
                        Route::post('/export-excel', 'ReportPresenceLessonController@presenceLessonReflectionToExcel');
                        Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonReflectionToPdf');
                    });
                    // stat
                    Route::prefix('stat')->group(function() {
                        Route::get('/', 'ReportPresenceLessonController@presenceLessonStat');
                        Route::post('/data', 'ReportPresenceLessonController@presenceLessonStatData');
                        Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonStatToPdf');
                        // class
                        Route::prefix('class')->group(function() {
                            Route::get('/', 'ReportPresenceLessonController@presenceLessonStatClass');
                            Route::post('/data', 'ReportPresenceLessonController@presenceLessonStatClassData');
                            Route::post('/export-pdf', 'ReportPresenceLessonController@presenceLessonStatClassToPdf');
                        });
                    });
                });
            });
            // assessment
            Route::prefix('assessment')->group(function() {
                // plan
                Route::prefix('plan')->group(function() {
                    // average
                    Route::prefix('average')->group(function() {
                        // class
                        Route::prefix('class')->group(function() {
                            Route::get('/', 'ReportAssessmentController@planClass');
                            Route::post('/data', 'ReportAssessmentController@planClassData');
                            Route::post('/graph', 'ReportAssessmentController@planClassGraph');
                            Route::post('/export-pdf', 'ReportAssessmentController@planClassToPdf');
                        });
                        // student
                        Route::prefix('student')->group(function() {
                            Route::get('/', 'ReportAssessmentController@planStudent');
                            Route::post('/graph', 'ReportAssessmentController@planStudentGraph');
                            Route::post('/export-pdf', 'ReportAssessmentController@planStudentToPdf');
                        });
                    });
                });
                // score
                Route::prefix('score')->group(function() {
                    Route::get('/', 'ReportAssessmentController@score');
                    Route::get('/detail', 'ReportAssessmentController@scoreDetail');
                    Route::post('/data', 'ReportAssessmentController@scoreData');
                    Route::post('/export-pdf', 'ReportAssessmentController@scoreToPdf');
                    // average
                    Route::prefix('average')->group(function() {
                        Route::get('/', 'ReportAssessmentController@scoreAverage');
                        Route::get('/detail', 'ReportAssessmentController@scoreAverageDetail');
                        Route::post('/data', 'ReportAssessmentController@scoreAverageData');
                        Route::post('/export-pdf', 'ReportAssessmentController@scoreAverageToPdf');
                    });
                    // legger
                    Route::prefix('legger')->group(function() {
                        Route::get('/', 'ReportAssessmentController@scoreLegger');
                        Route::get('/view', 'ReportAssessmentController@scoreLeggerView');
                        Route::post('/data', 'ReportAssessmentController@scoreLeggerData');
                        Route::post('/export-excel', 'ReportAssessmentController@scoreLeggerToExcel');
                        // lesson
                        Route::prefix('lesson')->group(function() {
                            Route::get('/', 'ReportAssessmentController@scoreLeggerLesson');
                            Route::get('/view', 'ReportAssessmentController@scoreLeggerLessonView');
                            Route::post('/export-excel', 'ReportAssessmentController@scoreLeggerLessonToExcel');
                        });
                        // class
                        Route::prefix('class')->group(function() {
                            Route::get('/', 'ReportAssessmentController@scoreLeggerClass');
                            Route::get('/view', 'ReportAssessmentController@scoreLeggerClassView');
                            Route::post('/export-excel', 'ReportAssessmentController@scoreLeggerClassToExcel');
                        });
                    });
                });
            });
        });
    });
});
