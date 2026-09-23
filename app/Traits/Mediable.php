<?php


namespace App\Traits;


use App\Models\Media;

trait Mediable
{
    public function allMedia()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function images()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'image');
    }

    public function profile()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'profile');
    }

    public function banners()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'banner');
    }

    public function galleries()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'gallery');
    }

    public function cover()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'cover');
    }


    public function videos()
    {
        return $this->morphMany(Media::class, 'mediable')->where('type', 'video');
    }

}
