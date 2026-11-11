<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SqlLike
{
    /**
     * Sanitize and bound a user-supplied search keyword for safe SQL LIKE matching.
     * Returns null when the input contains only SQL wildcards or whitespace.
     */
    public static function pattern(?string $term, int $maxLength = 100): ?string
    {
        $clean = preg_replace('/[\x00-\x1F\x7F]+/', ' ', (string) $term) ?? '';
        $clean = mb_substr(trim($clean), 0, $maxLength);
        $stripped = trim($clean, '%_\\');

        if ($stripped === '') {
            return null;
        }

        $driver = DB::connection()->getDriverName();
        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            $escaped = addcslashes($stripped, '\\%_');

            return "%{$escaped}%";
        }

        return "%{$stripped}%";
    }
}
