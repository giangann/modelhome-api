<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['author','title','thumb','summary'];

    public function post(){
        return $this->morphOne(Post::class,'postable');
    }
    public function tags(){
        return $this->morphToMany(Tag::class,'model','model_has_tags');
    }

}
