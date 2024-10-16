<?php

namespace App\Models\Passport;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\DocumentModel;

class PersonalAccessClient extends \Laravel\Passport\PersonalAccessClient
{
    use DocumentModel;
    protected $primaryKey = '_id';
    protected $keyType = 'string';
}
