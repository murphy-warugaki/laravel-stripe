<?php
namespace App\Http\Manager;

use App\Http\Manager\HmlManager;
use App\Http\Eloquent\Sample;
use App\Logs\HmlLog;


class SampleManager extends HmlManager
{


  protected $log;
  protected $saveData = [];

  public function __construct(  ){

    //シングルトン制御
    $this->log = new HmlLog();
    return app()->singleton('App\Http\Manager\SampleManager');
    
  }

  public function createRecord(){


    $params = [
                    ["name" => "tanaka","email" => "s11@gmail.com"],
                    ["name" => "yoshida","email" => "s22@gmail.com"]
    ];

    $params2 = [
                    ["id"=>15, 'name'=>"cr"],
                    ["name" => "aabb","email" => "s33@gmail.com"]
    ];

    $params3 = Sample::where('name','cc')->get();
    foreach ($params3 as $key => $value) {
      $params3[$key]['name'] = 'dd';
    }
    $this->setData(get_class( Sample::getInstance() ),$params);
    $this->setData(get_class( Sample::getInstance() ),$params2);
    $this->setData(get_class( Sample::getInstance() ),$params3);

    $this->log->setLog('sample',['data'=>'aaa']);
    $this->log->setLog('sample',['data'=>'bbb']);

  }
    
}