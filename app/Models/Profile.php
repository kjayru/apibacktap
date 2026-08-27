<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'firstname',
    'middlename',
    'lastname',
    'gender',
    'birthday',
    'ssn',
    'social_number',
    'address1',
    'address2',
    'city',
    'state',
    'zipcode',
    'drivernumber',
    'driverstate',
    'phone',
    'email',
    'organization',
    'emergencycontact',
    'emergencyphone',
    'relationship',
    'handguncaliber',
    'handguntype',
    'handgunrental',
    'shootingshotgun',
    'shotgungauce',
    'shotgunrental',
])]
class Profile extends Model
{

    /** Columnas de la tabla; sin esto Filament falla al crear o editar. */
    protected $fillable = [
        'user_id',
        'firstname',
        'middlename',
        'lastname',
        'gender',
        'birthday',
        'ssn',
        'address1',
        'address2',
        'city',
        'state',
        'zipcode',
        'drivernumber',
        'driverstate',
        'phone',
        'email',
        'organization',
        'emergencycontact',
        'emergencyphone',
        'relationship',
        'handguncaliber',
        'handguntype',
        'handgunrental',
        'shootingshotgun',
        'shotgungauce',
        'shotgunrental',
        'user',
        'social_number',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
