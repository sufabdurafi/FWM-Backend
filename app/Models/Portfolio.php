<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function images()
    {
        return $this->hasMany(PortfolioImage::class);
    }

    public function videos()
    {
        return $this->hasMany(PortfolioVideo::class);
    }
}
