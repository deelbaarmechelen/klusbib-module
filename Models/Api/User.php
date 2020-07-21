<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;
use Watson\Validating\ValidatingTrait;

class User extends BaseModel
{
    const STATE_ACTIVE_AVATAR = "DBM_avatar_ok.png";
    const STATE_INACTIVE_AVATAR = "DBM_avatar_nok.png";

    protected $endpoint = 'users';
    protected $primaryKey = 'user_id';

    // model validation: https://github.com/dwightwatson/validating
    use ValidatingTrait;

    protected $rules = array(
        'firstname'   => 'required|string|min:3|max:255',
        'lastname'   => 'string|nullable',
        'state' => 'required|string',
    );

    //    public function save()
//    {
//        return API::post('/items', $this->attributes);
//    }

}