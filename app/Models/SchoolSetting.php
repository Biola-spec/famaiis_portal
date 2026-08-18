<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name', 'motto', 'logo_path', 'address', 'report_tone', 'primary_color',
    ];

    public function getLogoAttribute(): ?string
    {
        return $this->logo_path;
    }

    public function getSchoolAddressAttribute(): ?string
    {
        return $this->address;
    }
}
