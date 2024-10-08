<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory;

    protected $table = 'lugares'; 

    public function datos()
    {
        return $this->hasMany(Dato::class, 'lugares_id');
    }
}
