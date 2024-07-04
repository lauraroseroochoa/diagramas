<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'anunciantes_productos'; 

    public function datos()
    {
        return $this->hasMany(Dato::class, 'producto2');
    }
}




