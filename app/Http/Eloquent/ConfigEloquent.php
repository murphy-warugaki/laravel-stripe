<?php

namespace App\Http\Eloquent;

use Illuminate\Database\Eloquent\Model;

class ConfigEloquent extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    public static function getRecord(string $table)
    {
        $model = new self();
        $model->table = 'config_'.$table;
        $result = $model->find(1);
        if (!$result) {
            $result = $model;
        }
        return $result;
    }
}
