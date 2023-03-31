<?php

namespace App\Models;

use http\Message\Body;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function taggable(){
        return $this->morphTo();
    }

    public function projects(){
        return $this->morphedByMany(Project::class,'model','model_has_tags');
    }

    public function blogs(){
        return $this->morphedByMany(Blog::class,'model','model_has_tags');
    }

}
