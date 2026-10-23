<?php

namespace App\Utils;

/**
 * Profile and company links come from user input. Rows saved before the URL
 * validation may hold javascript: or data: URLs, so views link a value only
 * when it is an http or https URL.
 */
class WebUrl {
    public static function orNull(?string $url): ?string {
        $url = trim((string) $url);

        return preg_match('#^https?://#i', $url) === 1 ? $url : null;
    }
}
