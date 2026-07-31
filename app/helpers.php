<?php

use App\Models\AcademicSetting;
use App\Models\StudentYear;
use App\Models\Term;

if (!function_exists('getCurrentSession')) {
    function getCurrentSession(): ?StudentYear
    {
        $setting = AcademicSetting::query()->first();

        if (!$setting || !$setting->current_session_id) {
            return StudentYear::query()->where('is_active', true)->first();
        }

        return StudentYear::query()->find($setting->current_session_id);
    }
}

if (!function_exists('getCurrentTerm')) {
    function getCurrentTerm(): ?Term
    {
        return null; // Term concept removed as per user request
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        return \App\Models\WebsiteSetting::where('key', $key)->value('value') ?? $default;
    }
}
