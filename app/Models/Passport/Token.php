<?php

namespace App\Models\Passport;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\DocumentModel;

class Token extends \Laravel\Passport\Token
{
    use DocumentModel;
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
