<?php

namespace App\Http\Traits;

trait PdfTrait 
{

    public function pdfPortrait($hashname, $filename)
    {
        $wkhtml = config('app.wkthmltopdf', 'wkhtmltopdf');
        return shell_exec('"'.$wkhtml.'" --enable-local-file-access -O portrait -s A4 -B 1.5cm -L 1cm -R 1cm -T 1.5cm  '.storage_path('app/public/tempo/').$hashname.'.html '.storage_path('app/public/downloads/').$filename);
    }

    public function pdfPortraits($hashname, $filename)
    {
        $wkhtml = config('app.wkthmltopdf', 'wkhtmltopdf');
        return shell_exec('"'.$wkhtml.'" --enable-local-file-access -O portrait -s A4 -B 1.5cm -L 1cm -R 1cm -T 1.5cm --footer-center ---Hal.[page]/[topage]--- --footer-font-size 8 --footer-font-name "Segoe UI"  '.storage_path('app/public/tempo/').$hashname.'.html '.storage_path('app/public/downloads/').$filename);
    }

    public function pdfLandscape($hashname, $filename)
    {
        $wkhtml = config('app.wkthmltopdf', 'wkhtmltopdf');
        return shell_exec('"'.$wkhtml.'" --enable-local-file-access -O landscape -s A4 -B 1.5cm -L 1cm -R 1cm -T 1.5cm --footer-center ---Hal.[page]/[topage]--- --footer-font-size 8 --footer-font-name "Segoe UI" '.storage_path('app/public/tempo/').$hashname.'.html '.storage_path('app/public/downloads/').$filename);
    }

    public function pdfCustomHeadFoot($hashname, $filename, $orientation, $size, $param)
    {
        $wkhtml = config('app.wkthmltopdf', 'wkhtmltopdf');
        return shell_exec('"'.$wkhtml.'" --enable-local-file-access -O '.$orientation.' -s '.$size.' '.$param.' --header-html '.storage_path('app/public/tempo/header_').$hashname.'.html --footer-html '.storage_path('app/public/tempo/footer_').$hashname.'.html '.storage_path('app/public/tempo/body_').$hashname.'.html '.storage_path('app/public/downloads/').$filename);
    }

}