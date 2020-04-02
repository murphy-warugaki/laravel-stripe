<?php

namespace App\Logs;

use App\HmlEloquent;
use Carbon\Carbon;

class HmlLog extends HmlEloquent
{

    protected $log_data = [];
    protected $connection = 'log';

    public function setLog($table_name,$value){

        $now = Carbon::now();

        if( !isset($this->log_data[$table_name]) ){
            $this->log_data[$table_name] = [];
        }
        //ID,deletedは削除、created_at、updated_atは現在の日付に
        unset($value['id']);
        unset($value['deleted_at']);
        $value['created_at'] = $now;
        $this->log_data[$table_name][] = $value;
    }

    public function saveLog(){

        foreach ($this->log_data as $key => $value) {
            \DB::connection($this->connection)->table($this->connection.'_'.$key)->insert($value);
        }
        //Save対象を削除
        $this->log_data = [];

    }
   
}
