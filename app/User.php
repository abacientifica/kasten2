<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\Roles;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = "usuarios";
    
    //PAra que no almacene de forma automatica la fecha de creacion y actualizacion
    public $timestamps = false;
     //La declaramos para decirle que la llave primaria es la que le indiquemos
     protected $primaryKey ='Usuario';

     //La declaramos para decirle que la primary key no es incremental por ejempli Id
     public $incrementing = false;

    protected $fillable = [
        'Usuario',
        'Tipo',
        'Nombres',
        'Apellidos',
        'FacProVenc',
        'Contrasena',
        'Notificaciones',
        'Correos',
        'IdTercero',
        'IdDireccion',
        'Inactivo',
        'Email',
        'InfoNovedadItem',
        'TipoExterno',
        'ExtensionTel',
        'Cargo',
        'password',
        'IdRol'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','Contrasena'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function rol(){
        //return $this->hasOne('App\Model\Roles','id','IdRol');
        return $this->belongsTo(Roles::class,'IdRol');
    }
    
}
