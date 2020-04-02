<?php
namespace App\Http\Manager;

use App\Logs\HmlLog;

class HmlManager
{
    protected $log;
    protected $saveData = [];

    public function __construct(HmlLog $hmlLog)
    {
        $this->log = $hmlLog;
    }

    //トランザクションでまとめてセーブする用
    protected function setData($className, $data)
    {
        if (!isset($this->saveData[$className])) {
            $this->saveData[$className] = [];
        }
        if (!is_array($data)) {
            $data = $data->toArray();
        }

        $this->saveData[$className] = array_merge($this->saveData[$className], $data);
    }

    public function saveData()
    {
        //dd($this->saveData);
        foreach ($this->saveData as $key => $value) {
            $obj = new $key;
            $obj->bulk_save($value);
        }
        $this->saveData = [];
    }

    public function saveLog()
    {
        $this->log->saveLog();
    }
}
