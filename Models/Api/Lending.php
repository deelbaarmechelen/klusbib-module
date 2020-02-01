<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;

class Lending extends BaseModel
{
    protected $endpoint = 'lendings';
    protected $primaryKey = 'lending_id';

}