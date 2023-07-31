<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rol extends Model
{
    use HasFactory;
    protected $table = 'rols';
    public $timestamps= false;
    public $fillable = ['rol'];

    public function users()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
