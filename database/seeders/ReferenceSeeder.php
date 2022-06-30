<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reference;

class ReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Reference::insertOrIgnore([
            [ 'code' => '-', 'name' => 'ASN', 'category' => 'hr_job', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Pegawai Swasta', 'category' => 'hr_job', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Wirausaha', 'category' => 'hr_job', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Ibu Rumah Tangga', 'category' => 'hr_job', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Lainnya', 'category' => 'hr_job', 'remark' => '-', 'order' => 5, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Berkecukupan', 'category' => 'hr_economic', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Kurang Mampu', 'category' => 'hr_economic', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Mukim', 'category' => 'hr_student_status', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Non Mukim', 'category' => 'hr_student_status', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'SD', 'category' => 'hr_education', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'SMP', 'category' => 'hr_education', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'SMA', 'category' => 'hr_education', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'D1', 'category' => 'hr_education', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'D2', 'category' => 'hr_education', 'remark' => '-', 'order' => 5, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'D3', 'category' => 'hr_education', 'remark' => '-', 'order' => 6, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'S1', 'category' => 'hr_education', 'remark' => '-', 'order' => 7, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'S2', 'category' => 'hr_education', 'remark' => '-', 'order' => 8, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'S3', 'category' => 'hr_education', 'remark' => '-', 'order' => 9, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Anak Kandung', 'category' => 'hr_child_status', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Anak Angkat', 'category' => 'hr_child_status', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Anak Tiri', 'category' => 'hr_child_status', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Lainnya', 'category' => 'hr_child_status', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'A', 'category' => 'hr_blood', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'B', 'category' => 'hr_blood', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'O', 'category' => 'hr_blood', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'AB', 'category' => 'hr_blood', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Belum ada data', 'category' => 'hr_blood', 'remark' => '-', 'order' => 5, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Orang Tua Kandung', 'category' => 'hr_parent_status', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Orang Tua Angkat', 'category' => 'hr_parent_status', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Orang Tua Tiri', 'category' => 'hr_parent_status', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Lainnya', 'category' => 'hr_parent_status', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Pindah Sekolah', 'category' => 'hr_student_mutation', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Dikeluarkan', 'category' => 'hr_student_mutation', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Mengundurkan Diri', 'category' => 'hr_student_mutation', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Pindah Tempat Tinggal', 'category' => 'hr_student_mutation', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Meninggal Dunia', 'category' => 'hr_student_mutation', 'remark' => '-', 'order' => 5, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Mengajar', 'category' => 'hr_teaching_status', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Asistensi', 'category' => 'hr_teaching_status', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Tambahan', 'category' => 'hr_teaching_status', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Istimewa', 'category' => 'hr_social_comment_type', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Baik', 'category' => 'hr_social_comment_type', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Cukup', 'category' => 'hr_social_comment_type', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Kurang', 'category' => 'hr_social_comment_type', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Tidak Baik', 'category' => 'hr_social_comment_type', 'remark' => '-', 'order' => 5, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Akademik', 'category' => 'hr_section', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Non Akademik', 'category' => 'hr_section', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Jawa', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Sunda', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Batak', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Madura', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 4, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Betawi', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 5, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Minangkabau', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 6, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Bugis', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 7, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Melayu', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 8, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Banten', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 9, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Banjar', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 10, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Bali', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 11, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Sasak', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 12, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Dayak', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 13, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Tionghoa', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 14, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Makassar', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 15, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Kaili', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 16, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Cirebon', 'category' => 'hr_tribe', 'remark' => '-', 'order' => 17, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Asisten', 'category' => 'hr_teacher_status', 'remark' => '-', 'order' => 1, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Guru Honorer', 'category' => 'hr_teacher_status', 'remark' => '-', 'order' => 2, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
            [ 'code' => '-', 'name' => 'Guru Pelajaran', 'category' => 'hr_teacher_status', 'remark' => '-', 'order' => 3, 'parent' => 0, 'created_at' => date('Y-m-d H:i:s') ],
        ]);
    }
}
