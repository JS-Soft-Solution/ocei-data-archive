<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Get validated, bounded pagination limit (default 25, clamped between 5 and 100).
     */
    protected function getSafePerPage(Request $request, int $default = 25, int $max = 100): int
    {
        $perPage = (int) $request->get('per_page', $default);
        if ($perPage <= 0) {
            $perPage = $default;
        }
        return min(max($perPage, 5), $max);
    }
}
