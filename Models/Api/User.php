<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;

class User extends BaseModel
{
    protected $endpoint = 'users';
    protected $primaryKey = 'user_id';

//    public function save()
//    {
//        return API::post('/items', $this->attributes);
//    }

}