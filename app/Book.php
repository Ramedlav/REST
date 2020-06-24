<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'name', 'user_id', 'author_id', 'qty_pages', 'annotation', 'img'
    ];

    public function author()
    {
        return $this->belongsTo('App\Author');
    }
}
