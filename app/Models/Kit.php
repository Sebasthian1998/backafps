<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    use HasFactory;



    protected $table = 'kit';

    public function users(){
        return $this->belongsToMany(User::class, 'user_kit');
    }
}
