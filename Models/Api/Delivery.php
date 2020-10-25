<?php

namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;
use Watson\Validating\ValidatingTrait;

class Delivery extends BaseModel
{
    // model validation: https://github.com/dwightwatson/validating
    use ValidatingTrait;

    protected $rules = array(
        'asset_id' => 'required|exists:users',
        'user_id' => 'required',
    );
}