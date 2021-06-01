<?php
namespace App\Traits;


trait FormattedDates {
    protected function serializeDate(\DateTimeInterface $date) {
        $carbonInstance = \Carbon\Carbon::instance($date);
        return $carbonInstance->format('d/m/Y');
    }
}