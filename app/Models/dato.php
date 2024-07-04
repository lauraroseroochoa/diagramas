<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dato extends Model
{
    use HasFactory;

    protected $table = 'datos'; 

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto2');
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'lugares_id');
    }
}
