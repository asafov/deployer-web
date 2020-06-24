<?php
use Symfony\Component\Process\PhpExecutableFinder;
use \App\Models\Setting;

if (!function_exists('phpPath')) {
    function phpPath () {
        $phpBinaryFinder = new \Symfony\Component\Process\PhpExecutableFinder ();
        return $phpBinaryFinder->find();
    }
}

if (!function_exists('depPath')) {
    function depPath () {
        return base_path('vendor/bin/dep');
    }
}

if (! function_exists('get_system_setting')) {
    function get_system_setting ($key, $default = NULL) {
        $setting = Setting::where('slug', $key)->first ();

        if (!is_null ($setting)) {
            return $setting->data['value'] ?? $default;
        }

        return $default;
    }
}
