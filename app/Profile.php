<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function profileImage() {
        return ($this->image) ? '/storage/' . $this->image : '/storage/profile/eXaxZxmyrFnRUO60O600iJNJTTOtSSco2ZRIYB1v.jpeg';
    }

    public function followers() {
        return $this->belongsToMany(User::class);
    }
}
