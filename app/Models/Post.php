<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    const MODEL_TYPE = [
        'PROJECT'=>'Project',
        'POST'=>'Post'
    ];

    protected $fillable = ['postable_type','postable_id','content'];

    public function postable(){
        return $this->morphTo();
    }
}
