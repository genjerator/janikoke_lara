<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeopleInfo extends Model
{
    protected $table = 'people_info';

    protected $fillable = [
        'person_id',
        'description',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}
