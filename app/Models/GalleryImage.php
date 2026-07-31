<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $guarded = [];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id', 'id');
    }
}
