<?php

namespace App\Logs;

use Illuminate\Database\Eloquent\Model;


class ErrorLog extends Model
{

    const UPDATED_AT = null;

    protected $table = "error_log";
    protected $connection = 'error';   
}
