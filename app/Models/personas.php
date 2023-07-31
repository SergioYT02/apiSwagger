<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personas extends Model
{
    use HasFactory;
    protected $table = 'personas';
    public $timestamps= false;
    public $fillable =[
        'nombre','cedula','direccion','fecha_nacimiento'
    ];
    public function user()
    {
        return $this->hasOne(User::class, 'id_persona');
    }
}
