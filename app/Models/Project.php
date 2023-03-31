<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['thumb','name','summary','slug','location','customer_name','square','finish_in','is_main'];

    public function post(){
        return $this->morphOne(Post::class,'postable');
    }

    public function tags(){
        return $this->morphToMany(Tag::class,'model','model_has_tags');
    }
}
