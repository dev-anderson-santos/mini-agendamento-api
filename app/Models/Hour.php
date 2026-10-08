<?php

namespace App\Models;

use Database\Factories\HourFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['hour'])]
class Hour extends Model
{
    /** @use HasFactory<HourFactory> */
    use HasFactory, HasUuids;

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
