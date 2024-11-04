<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = "files";

    protected $fillable = [
        "name",
        "description",
        "file",
        "is_active",
        "created_by",
    ];

    protected $casts = [
        "is_active" => "boolean",
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
