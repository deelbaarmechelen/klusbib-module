<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;

class Reservation extends BaseModel
{
    protected $endpoint = 'reservations';
    protected $primaryKey = 'reservation_id';

}