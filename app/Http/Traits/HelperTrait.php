<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Institute;
use App\Models\User;
use App\Models\Notification;
use App\Repositories\Notification\NotificationEloquent;
use App\Http\Controllers\UpdaterController;
use Modules\HR\Entities\Employee;
use Modules\Finance\Entities\BookYear;
use Carbon\Carbon;
use ConnectException;
use Exception;

define("MIN_DATES_DIFF", 25569);
define("SEC_IN_DAY", 86400);

trait HelperTrait 
{

    public function getPeriodName($param)
    {
        $value = sprintf('%06d', $param);
        return $this->getMonthName(substr($value, 0,2)) .' / '. substr($value, 2,4);
    }

    public function getConfigs($slug, $key = '#')
    {
        $query = DB::table('public.configs')->where('slug', $slug);
        if ($key != '#')
        {
            $query = $query->where('key', $key);
        }
        return $query->first();
    }

    public function getSurah($surah_id)
    {
        if (!empty($surah_id))
        {
            return DB::table('public.quran_surahs')->where('id', $surah_id)->first();
        } else {
            $surahs = new \stdClass;
            $surahs->id = 0;
            $surahs->surah = '-';
            $surahs->total = 0;
            return $surahs;
        }
    }

    public function getJuz($juz_id)
    {
        return DB::table('public.quran_juzs')->where('id', $juz_id)->first();
    }

    public function getActiveBookYear()
    {
        $bookyear = new BookYear();
        return BookYear::where('is_active',1)->firstOr( function() use ($bookyear) {
            $bookyear->id = 0;
            $bookyear->book_year = '1970';
            $bookyear->start_date = '1970-01-01';
            $bookyear->end_date = '1970-01-01';
            $bookyear->prefix = '70';
            return $bookyear;
        });
    }

    public function getPrefixBookYear($bookyear_id)
    {
        return BookYear::find($bookyear_id)->pluck('prefix')->first();
    }

    public function checkUpdate()
    {
        if ($this->check_internet(config('app.updater_url'), 443))
        {
            $user = User::find(auth()->user()->id);
            if ($user->getRoleNames()[0] == 'Administrator')
            {
                $updater = new UpdaterController();
                $newVersion = $updater->check();
                if (!empty($newVersion))
                {
                    $request = new Request();
                    $request->merge([
                        'user_id' => auth()->user()->id,
                        'msg_type' => 'update',
                        'items' => json_encode(array($newVersion)),
                        'is_read' => 0,
                    ]);
                    $notification_search = Notification::where('msg_type', 'update')->where('user_id', auth()->user()->id);
                    $notification = new NotificationEloquent();
                    if (!empty($notification_search->items))
                    {
                        if ($newVersion != json_decode($notification_search->items)[0])
                        {
                            $notification->create($request);
                        }
                    } else {
                        $notification->create($request);
                    }
                }
            }
        }
    }

    private function check_internet($domain, $port)
    {
        $hostnames = explode('//', $domain);
        $file = @fsockopen ($hostnames[1], $port);
        return ($file);
    }

    public function PHPExcelCommonStyle()
    {
        return [
            'bold' => [ 'font' => [ 'bold' => true ] ],
            'normal' => [ 'font' => [ 'bold' => false ] ],
            'title' => [ 'font' => [ 'bold' => true, 'size' => 13 ] ],
            'subTitle' => [ 'font' => [ 'bold' => true, 'size' => 12 ] ],
            'header' => [ 'font' => [ 'bold' => true, 'size' => 12 ], 'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ], 'borders' => [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ] ], 'fill' => [ 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => [ 'argb' => 'CCFFFF' ] ] ],
            'bodyLeft' => [ 'font' => [ 'size' => 11 ], 'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ], 'borders' => [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ] ] ],
            'bodyRight' => [ 'font' => [ 'size' => 11 ], 'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ], 'borders' => [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ] ] ],
            'bodyCenter' => [ 'font' => [ 'size' => 11 ], 'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ], 'borders' => [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ] ] ],
            'bodyCenterBig' => [ 'font' => [ 'bold' => true, 'size' => 16 ], 'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ], 'borders' => [ 'allBorders' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ] ] ],
            'contentRight' => [ 'font' => [ 'size' => 11 ], 'alignment' => [ 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, ] ],
        ];
    }

    public function getEmployeeName($employee_id)
    {
        $employee    = Employee::where('id',$employee_id)->orWhere('employee_id',$employee_id)->first();
        $title_first = !empty($employee->title_first) ? $employee->title_first : '';
        $name        = !empty($employee->name) ? $employee->name : '';
        $title_end   = !empty($employee->title_end) ? $employee->title_end : '';
        return $title_first .' '. $name .' '. $title_end;
    }

    public function getEmployeeNo($employee_id)
    {
        $employee = Employee::where('id',$employee_id)->orWhere('employee_id',$employee_id)->first();
        return $employee->employee_id;
    }

	public function getInstituteProfile()
    {
    	$foundation = Institute::whereHas('getDepartment', function($qry) {
                            $qry->where('is_all',1);    
                        })->first();
        if (Storage::disk('local')->exists('/public/uploads/'.$foundation->logo) && $foundation->logo <> null)
        {
            $logo = asset('storage/uploads') .'/'. $foundation->logo;
        } else {
            $logo = asset('img/logo-yayasan.png');
        }
        $profiles = array(
            'name' => Str::title($foundation->name),
            'logo' => $logo,
            'address' => $foundation->address,
            'phone' => $foundation->phone,
            'fax' => !empty($foundation->fax) ? $foundation->fax : '-',
            'web' => $foundation->website,
            'email' => $foundation->email
        );
        return $profiles;
    }

    public function getResponse($method, $message = "", $subject = "", $params = "")
    {
    	switch ($method) 
    	{
    		case 'warning':
    			return [ 'success' => false, 'message' => $message ];
    			break;
    		case 'login':
    			return [ 'success' => true, 'message' => 'Berhasil masuk' ];
    			break;
    		case 'logout':
    			return [ 'success' => true, 'message' => 'Berhasil keluar' ];
    			break;
    		case 'destroy':
    			return [ 'success' => true, 'message' => $subject . ' berhasil dihapus', 'params' => $params ];
    			break;
            case 'info':
                return [ 'success' => true, 'message' => $message ];
                break;
    		case 'error':
    			switch ($message)
    			{
                    case Str::contains($message, 'Data missing'):
                        return [ 'success' => false, 'message' => 'Isian tanggal tidak lengkap, silahkan cek kolom tanggal.' ];
                        break;
    				case Str::contains($message, 'Unique violation'):
		                return [ 'success' => false, 'message' => 'Input '.$subject.' sudah digunakan.' ];
		                break;
		            case Str::contains($message, 'Foreign key violation'):
		                return [ 'success' => false, 'message' => $subject.' terpilih sudah digunakan pada data (transaksi) lain.' ];
		                break;   
		            case Str::contains($message, 'Raise exception'):
		                $exception = Str::between($message, 'ERROR:', 'CONTEXT:');
		                return [ 'success' => false, 'message' => $exception ];
		                break;  
		            default:
		                return [ 'success' => false, 'message' => $message ];
		                break;
    			}
    			break;
    		default:
    			return [ 'success' => true, 'message' => $subject . ' berhasil disimpan', 'params' => $params ];
    			break;
    	}
    }

    public function gridRequest(Request $request, $order = 'desc', $sort_col = '')
    {
        return array(
            'page' => $request->has('page') ? intval($request->page) : 1,
            'rows' => $request->has('rows') ? intval($request->rows) : 10,
            'sort' => $request->has('sort') ? $request->sort : ($sort_col != '' ? strval($sort_col) : 'id'),
            'sort_by' => $request->has('order') ? $request->order : $order,
        );
    }

    public function removeFormat($value) 
    {
        return str_replace(',', '', str_replace('Rp', '', $value));
    }

    private function denominator($value) 
    {
        $value = abs($value);
        $letter = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($value < 12) 
        {
            $temp = " ". $letter[$value];
        } else if ($value <20) {
            $temp = $this->denominator($value - 10). " belas";
        } else if ($value < 100) {
            $temp = $this->denominator($value/10)." puluh". $this->denominator($value % 10);
        } else if ($value < 200) {
            $temp = " seratus" . $this->denominator($value - 100);
        } else if ($value < 1000) {
            $temp = $this->denominator($value/100) . " ratus" . $this->denominator($value % 100);
        } else if ($value < 2000) {
            $temp = " seribu" . $this->denominator($value - 1000);
        } else if ($value < 1000000) {
            $temp = $this->denominator($value/1000) . " ribu" . $this->denominator($value % 1000);
        } else if ($value < 1000000000) {
            $temp = $this->denominator($value/1000000) . " juta" . $this->denominator($value % 1000000);
        } else if ($value < 1000000000000) {
            $temp = $this->denominator($value/1000000000) . " milyar" . $this->denominator(fmod($value,1000000000));
        } else if ($value < 1000000000000000) {
            $temp = $this->denominator($value/1000000000000) . " trilyun" . $this->denominator(fmod($value,1000000000000));
        }     
        return $temp;
    }
 
    public function counted($value) 
    {
        if($value < 0) 
        {
            $result = "minus ". trim($this->denominator($value));
        } else {
            $result = trim($this->denominator($value));
        }           
        return $result;
    }

    public function filter_filename($filename) 
    {
        // sanitize filename
        $filename = preg_replace(
            '~
            [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
            [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
            [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
            [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
            [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
            ~x',
            '-', $filename);
        // avoids ".", ".." or ".hiddenFiles"
        $filename = ltrim($filename, '.-');
        // optional beautification
        // if ($beautify) 
        $filename = $this->beautify_filename($filename);
        // maximise filename length to 255 bytes http://serverfault.com/a/9548/44086
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
        return $filename;
    }

    public function beautify_filename($filename) 
    {
        // reduce consecutive characters
        $filename = preg_replace(array(
            // "file   name.zip" becomes "file-name.zip"
            '/ +/',
            // "file___name.zip" becomes "file-name.zip"
            '/_+/',
            // "file---name.zip" becomes "file-name.zip"
            '/-+/'
        ), '-', $filename);
        $filename = preg_replace(array(
            // "file--.--.-.--name.zip" becomes "file.name.zip"
            '/-*\.-*/',
            // "file...name..zip" becomes "file.name.zip"
            '/\.{2,}/'
        ), '.', $filename);
        // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
        $filename = mb_strtolower($filename, mb_detect_encoding($filename));
        // ".file-name.-" becomes "file-name"
        $filename = trim($filename, '.-');
        return $filename;
    }

    public function formatCode($param)
    {
        if (Str::contains($param, '-'))
        {
            $arr = explode('-', $param);
            return $arr[1];
        } else {
            return $param;
        }
    }

    public function isValidPeriod($param, $value)
    {
        $params = explode('/', $param);
        $values = explode('/', $value);
        return (strtotime(Carbon::createFromFormat('d/m/Y',$param)) > strtotime(Carbon::createFromFormat('d/m/Y',$value)) || $params[2] !== $values[2]) ? false : true;
    }

    public function formatLabel($param)
    {
        $string = preg_match('/\s/',$param);
        if ($string > 0)
        {
            return str_replace(' ', "\n", $param);
        } else {
            return $param;
        }
    }

    public function totalMinutes($time)
    {
        $time = explode(':', $time);
        return ($time[0]*60) + ($time[1]);
    }

    public function formatDate($date, $format)
    {
        switch ($format) 
        {
            case 'local':
                return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
                break;
            case 'localtime':
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y');
                break;
            case 'iso':
                return Carbon::createFromFormat('Y-m-d', $date)->isoFormat('DD-MMM-Y');
            case 'timeiso':
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->isoFormat('DD-MMM-Y');
                break;
            case 'month':
                return Carbon::createFromFormat('Y-m-d', $date)->isoFormat('MMM');
                break;
            case 'monthyear':
                return Carbon::createFromFormat('Y-m-d', $date)->isoFormat('MMMM Y');
                break;
            case 'isotime':
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->isoFormat('DD-MMM-Y HH:mm:ss');
                break;
            case 'localtimes':
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d/m/Y H:i:s');
                break;
            case 'dateday':
                return Carbon::createFromFormat('Y-m-d', $date)->isoFormat('dddd, D MMMM Y');
                break;
            default:
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                break;
        }
    }

    public function formatCurrency($value, $currency)
    {
        switch ($currency)
        {
            default:
                return 'Rp'. number_format($value,2);
                break;
        }
    }

    public function dateBefore($param)
    {
        if (str_contains($param, '/'))
        {
            return date('Y-m-t', strtotime(date("Y-m-d", strtotime('-1 month',strtotime($this->formatDate($param,'sys'))))));
        } else {
            return date('Y-m-t', strtotime(date("Y-m-d", strtotime('-1 month',strtotime($param)))));
        }
    }

    public function getDayName($value)
    {
        switch ($value) 
        {
            case 1:
                return 'Senin';
                break;
            case 2:
                return 'Selasa';
                break;
            case 3:
                return 'Rabu';
                break;
            case 4:
                return 'Kamis';
                break;
            case 5:
                return 'Jum`at';
                break;
            case 6:
                return 'Sabtu';
                break;
            default:
                return 'Ahad';
                break;
        }
    }

    public function getMonthName($value)
    {
        switch ($value) 
        {
            case '01':
                return 'Januari';
                break;
            case '02':
                return 'Pebruari';
                break;
            case '03':
                return 'Maret';
                break;
            case '04':
                return 'April';
                break;
            case '05':
                return 'Mei';
                break;
            case '06':
                return 'Juni';
                break;
            case '07':
                return 'Juli';
                break;
            case '08':
                return 'Agustus';
                break;
            case '09':
                return 'September';
                break;
            case '10':
                return 'Oktober';
                break;
            case '11':
                return 'Nopember';
                break;
            default:
                return 'Desember';
                break;
        }
    }
}