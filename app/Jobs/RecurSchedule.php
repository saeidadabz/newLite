<?php

namespace App\Jobs;

use App\Enums\RecurrencePattern;
use App\Models\Schedule;
use App\Params\RecurParam;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class RecurSchedule
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Schedule $schedule,
        private RecurParam $recurParam
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $param = $this->recurParam;
        $schedule = $this->schedule;
        /** @var Carbon $currDate */
        $currDate = $schedule->starts_at;
        $dateIntervalInSeconds = $currDate->diffInSeconds($schedule->ends_at);

        while (true) {
            switch ($param->pattern) {
                case RecurrencePattern::Daily->value:
                    $currDate->addDay();

                    break;
                case RecurrencePattern::Weekly->value:
                    $currDate->addWeek();

                    break;
                case RecurrencePattern::Monthly->value:
                    $currDate->addMonth();

                    break;
                case RecurrencePattern::CUSTOM->value:
                    // TODO - Custom recurrences

                    break;
                default:
                    throw new \Exception('Invalid recurrence pattern.');
            }

            if (! $currDate->lessThan($param->endDate)) {

                break;
            }

            $data = $schedule->toArray();
            $data['starts_at'] = $currDate;
            $endDate = clone $currDate;
            $data['ends_at'] = $endDate->addSeconds($dateIntervalInSeconds);

            Schedule::create($data);
        }
    }
}
