<?php
namespace app\models;

use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Db;

class Session extends Model
{
    protected $table   = 'session';

    public $timestamps = true;

    protected $fillable = ['id', 'device_id', 'user_id', 'expire', 'created_at', 'updated_at'];
}