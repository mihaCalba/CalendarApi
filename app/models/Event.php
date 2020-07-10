<?php
namespace app\models;

use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Db;

class Event extends Model
{
    protected $table   = 'event';

    public $timestamps = true;

    protected $fillable = ['name', 'location', 'description', 'status', 'from', 'to'];

    protected $hidden = ['pivot', 'password', 'verification_code', 'created_at', 'updated_at'];

    public function getFillableFields()
    {
        return $this->fillable;
    }
}
