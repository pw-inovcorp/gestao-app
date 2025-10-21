<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait HasCustomActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        $logAttributes = property_exists($this, 'logAttributes')
            ? $this->logAttributes
            : [];

        return LogOptions::defaults()
            ->logOnly($logAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => $eventName);
    }

    public function tapActivity($activity, string $eventName)
    {
        $activity->properties = $activity->properties->put('ip', request()->ip());
        $activity->properties = $activity->properties->put('user_agent', request()->userAgent());
    }

}
