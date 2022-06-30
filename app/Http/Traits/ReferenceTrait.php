<?php

namespace App\Http\Traits;

use App\Models\Reference;

trait ReferenceTrait {

    public function getReference($id)
    {
        return Reference::select('name')->where('id',$id)->first()->name;
    }

    public function getTransactionSource()
    {
        $result = array(
            'major_jtt' => 'Besar Pembayaran Santri',
            'major_jtt_prospect' => 'Besar Pembayaran Calon Santri',
            'receipt_jtt' => 'Penerimaan Iuran Wajib Santri',
            'receipt_jtt_prospect' => 'Penerimaan Iuran Wajib Calon Santri',
            'receipt_skr' => 'Penerimaan Iuran Sukarela Santri',
            'receipt_skr_prospect' => 'Penerimaan Iuran Sukarela Calon Santri',
            'receipt_others' => 'Penerimaan Lain-Lain',
            'expense' => 'Pengeluaran',
            'savingdeposit' => 'Setoran Tabungan',
            'savingwithdrawal' => 'Penarikan Tabungan',
            'journalvoucher' => 'Jurnal Umum',
            'begin_balance' => 'Saldo Awal',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getReferences($category)
    {
        $options = array();
        $query = Reference::select('id','name')->where('category',$category)->get();
        foreach ($query as $q) 
        {
            $options[$q->id] = $q->name;
        }
        return $options;
    }

    public function getGender()
    {
        $result = array(
            '1' => 'Laki-Laki',
            '2' => 'Perempuan',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getMarital()
    {
        $result = array(
            '1' => 'Belum Menikah',
            '2' => 'Sudah Menikah',
            '3' => 'Janda/Duda',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getActive()
    {
        $result = array(
            '1' => 'Aktif',
            '2' => 'Tidak Aktif',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getCitizen()
    {
        $result = array(
            '1' => 'WNI',
            '2' => 'WNA',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getColumnOption()
    {
        $result = array(
            '1' => 'TEKS',
            '2' => 'PILIHAN',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getMandatory()
    {
        $result = array(
            '1' => 'Wajib',
            '2' => 'Tambahan',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getFullDay()
    {
        $result = array(
            '1' => 'Senin',
            '2' => 'Selasa',
            '3' => 'Rabu',
            '4' => 'Kamis',
            '5' => 'Jum`at',
            '6' => 'Sabtu',
            '0' => 'Ahad',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function getPresence()
    {
        $result = array(
            '0' => 'Hadir',
            '1' => 'Ijin',
            '2' => 'Sakit',
            '3' => 'Cuti',
            '4' => 'Alpa',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

    public function setPresence($param)
    {
        switch ($param) {
            case 'Ijin':
                return 1;
                break;
            case 'Sakit':
                return 2;
                break;
            case 'Cuti':
                return 3;
                break;
            case 'Alpa':
                return 4;
                break;
            default:
                return 0;
                break;
        }
    }

    public function getCategory()
    {
        $result = array(
            'hr_job' => 'PEKERJAAN',
            'hr_economic' => 'KONDISI EKONOMI',
            'hr_student_status' => 'STATUS SANTRI',
            'hr_education' => 'PENDIDIKAN',
            'hr_child_status' => 'STATUS ANAK',
            'hr_blood' => 'GOLONGAN DARAH',
            'hr_parent_status' => 'STATUS ORANG TUA',
            'hr_student_mutation' => 'STATUS MUTASI SANTRI',
            'hr_teaching_status' => 'STATUS AJAR',
            'hr_social_comment_type' => 'KONDISI SOSIAL',
            'hr_section' => 'BIDANG',
            'hr_tribe' => 'SUKU',
            'hr_teacher_status' => 'STATUS GURU PELAJARAN',
        );
        $options = array();
        foreach ($result as $index => $row) 
        {
            $options[$index] = $row;
        }
        return $options;
    }

}