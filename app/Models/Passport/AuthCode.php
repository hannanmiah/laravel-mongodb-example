<?php

namespace App\Models\Passport;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\DocumentModel;

class AuthCode extends \Laravel\Passport\AuthCode
{
    use DocumentModel;
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
