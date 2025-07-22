<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioVideo extends Model
{
    protected $fillable = [
        'portfolio_id',
        'video_url',
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }
}
