<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Model;
use MongoDB\Laravel\Eloquent\DocumentModel;

class Client extends \Laravel\Passport\Client
{
    use DocumentModel;
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
