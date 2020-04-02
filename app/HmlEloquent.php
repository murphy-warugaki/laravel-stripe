<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\InsertOnDuplicateKey;
use Batch;
use App\Http\Eloquent\Sample;
use Carbon\Carbon;

class HmlEloquent extends Model
{   

    use SoftDeletes; //論理削除に対応する
    use InsertOnDuplicateKey; //Duplicate On Key Updateに対応するTraitを読み込み

    //変更がある場合は継承先でオーバライドすること
    protected $primaryKey = 'id';
    protected $bulk_insert_data = [];
    protected $bulk_update_data = [];

    //配列データで渡す
    function bulk_save($value)
    { 

      //クエリ数数えるなら下記利用 ddを最下部におけばクエリ数出ます
      //\DB::enableQueryLog();
      //dd(\DB::getQueryLog());

      $bulk_insert_data = [];
      $bulk_update_data = [];

      if(!is_array($value) ){
        $value = $value->toArray();
      }

      $now = Carbon::now();

      foreach ($value as $key => $record) {

        //$record['updated_at'] = $now;
        //主キーがない場合は新規レコード
        if( !isset($record[$this->primaryKey]) ){
          $record['created_at'] = $now;
          $this->bulk_insert_data[] = $record;
        }else{
          if(isset($record['created_at'])) unset( $record['created_at'] );
          $this->bulk_update_data[] = $record;
        }
        
      }

      //バルクインサート実行(Duplicate On Keyに対応)
      //ユニークkeyがかぶってた場合はupdateにする
      $this->insertOnDuplicateKey($this->bulk_insert_data);

      //主キー指定がある場合はバルクアップデート実行
     Batch::update($this,$this->bulk_update_data,$this->primaryKey);
       
    }

    //クラスからbulk_saveを直接呼べるようにする
    public static function getInstance()
    {
          $instance = new static;
          return $instance;
    }


}

