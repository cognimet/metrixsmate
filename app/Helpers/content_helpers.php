<?php

use Illuminate\Support\Facades\App;

if (! function_exists('transContent')) {
    /**
     * Translate database-driven content using the hi/content.php mapping.
     * Returns the original text when locale is 'en' or no mapping exists.
     */
    function transContent(string $text): string
    {
        if (App::getLocale() !== 'hi') {
            return $text;
        }

        $map = trans('content');

        // Direct match
        if (is_array($map) && isset($map[$text])) {
            return $map[$text];
        }

        return $text;
    }
}

if (! function_exists('transContentList')) {
    /**
     * Translate a semicolon-separated list (e.g., career paths, work environments).
     * Each item is individually translated, then recombined.
     */
    function transContentList(string $text, string $separator = ';'): string
    {
        if (App::getLocale() !== 'hi') {
            return $text;
        }

        $items = explode($separator, $text);
        $translated = array_map(function ($item) {
            return transContent(trim($item));
        }, $items);

        return implode($separator . ' ', $translated);
    }
}
