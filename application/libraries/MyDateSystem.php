<?php if(!defined('BASEPATH'))exit('No direct script acces allowed');

class MyDateSystem
{
    
    private $day;
    private $month;
    private $year;
    private $th_year;
    
    private $hour;
    private $minute;
    private $second;
    
    private $ThaiShortDate = array('มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม');
    private $ThaiFullDate = array('ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.');
                
    // Method to split date and seperate it.
    public function splitDateTime($input) {
        
        $splitDateTime = explode(' ', $input);
        $splitDate = !empty($splitDateTime[0])?explode('-', $splitDateTime[0]):'';
        $splitTime = !empty($splitDateTime[1])?explode(':', $splitDateTime[1]):'';
        
        // SETUP VARIABLE
        // DATE
        $this->day = strlen($splitDate[2]) < 2?str_replace('0', '', $splitDate[2]):$splitDate[2];
        $this->month = strlen($splitDate[1]) < 2?str_replace('0', '', $splitDate[1]):$splitDate[1];
        $this->year = $splitDate[0];
        $this->th_year = $splitDate[0] + 543;
        
        // TIME
        $this->hour = !empty($splitTime[0])?$splitTime[0]:'';
        $this->minute = !empty($splitTime[1])?$splitTime[1]:'';
        $this->second = !empty($splitTime[2])?$splitTime[2]:'';
        $this->fullTime = !empty($splitDateTime[1])?$splitDateTime[1]:'';
    }
    
    // Method to change data to default format
    public function restoreDate($delimiter, $input) {
        
        $dataSplit = explode($delimiter, $input);
        $defaultFormat = $dataSplit[2].'/'.$dataSplit[1].'/'.$dataSplit[0];
        
        return $defaultFormat;
    }
            
    // Method to change data to database
    public function thaiDate($input, $format = 1, $hasTime = false) {
        
        $this->splitDateTime($input);
        $indexMonth = $this->month - 1;
        
        if($format == 1){
            if($hasTime){
                $newFormat = $this->day.' '.$this->ThaiShortDate[$indexMonth].' '.$this->th_year.' เวลา '.$this->fullTime.' น.';
            }else{
                $newFormat = $this->day.' '.$this->ThaiShortDate[$indexMonth].' '.$this->th_year;
            }
        }elseif($format == 2){
            if($hasTime){
                $newFormat = $this->day.' '.$this->ThaiFullDate[$indexMonth].' พ.ศ. '.$this->th_year.' เวลา '.$this->fullTime.' น.';
            }else{
                $newFormat = $this->day.' '.$this->ThaiFullDate[$indexMonth].' พ.ศ. '.$this->th_year;
            }
        }elseif($format == 3){
            if($hasTime){
                $newFormat = $this->ThaiFullDate[$indexMonth].' พ.ศ. '.$this->year.' เวลา '.$this->fullTime.' น.';
            }else{
                $newFormat = $this->ThaiFullDate[$indexMonth].' พ.ศ. '.$this->year;
            }
        }
        
        return $newFormat;
    }
}
