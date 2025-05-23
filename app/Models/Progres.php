<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Progres extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    protected $table = 'progress';
    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
}
