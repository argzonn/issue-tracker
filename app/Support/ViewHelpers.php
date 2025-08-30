<?php

namespace App\Support;

final class ViewHelpers
{
    public static function statusClass(string $status): string
    {
        return match ($status) {
            'open'        => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'in_progress' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
            'blocked'     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'closed'      => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
            default       => 'bg-light text-body border border-light-subtle',
        } . ' rounded px-2 py-1 text-xs fw-medium';
    }

    public static function priorityClass(string $priority): string
    {
        return match ($priority) {
            'low'      => 'bg-success-subtle text-success-emphasis border border-success-subtle',
            'medium'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
            'high'     => 'bg-orange-subtle text-orange-emphasis border border-orange-subtle', // may fall back to custom CSS; Bootstrap has no orange by default
            'critical' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
            default    => 'bg-light text-body border border-light-subtle',
        } . ' rounded px-2 py-1 text-xs fw-medium';
    }
}
