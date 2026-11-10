<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {
    protected $fillable = ['name', 'code'];

    public function movementsOut() {
        return $this->hasMany(Movement::class, 'from_location_id');
    }

    public function movementsIn() {
        return $this->hasMany(Movement::class, 'to_location_id');
    }
}