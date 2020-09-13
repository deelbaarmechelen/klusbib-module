<?php

namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;
use Watson\Validating\ValidatingTrait;

class Reservation extends BaseModel
{
    protected $endpoint = 'deliveries';
    protected $primaryKey = 'delivery_id';

    /*     // model validation: https://github.com/dwightwatson/validating
    use ValidatingTrait;

    protected $rules = array(
        'asset_id' => 'required|exists:users',
        'cancel_reason' => 'string|max:50',
        'end_date' => 'date|nullable|after:start_date',
        'name'   => 'required|string|min:3|max:255',
        'notes'   => 'string|nullable',
        'start_date' => 'required|date|after_or_equal:today',
        'state' => 'required|string',
        'user_id' => 'required|date',
    ); */
}
