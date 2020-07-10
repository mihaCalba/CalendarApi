<?php
namespace app\models;

use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Db;

class User extends Model
{
    protected $table   = 'user';

    public $timestamps = true;

    protected $fillable = ['firstname', 'lastname', 'email', 'phone', 'active', 'password'];

    protected $hidden = ['pivot', 'password', 'verification_code', 'created_at', 'updated_at'];

    public function getFillableFields()
    {
        return $this->fillable;
    }

    public function isActive()
    {
        return ! empty($this->active);
    }


}
