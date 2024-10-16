<?php

namespace App\Models\Passport;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\DocumentModel;

class RefreshToken extends \Laravel\Passport\RefreshToken
{
    use DocumentModel;
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
