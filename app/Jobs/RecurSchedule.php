<?php

namespace App\Jobs;

use App\Enums\RecurrenceDay;
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
        private Schedule   $schedule,
        private RecurParam $recurParam
    )
    {
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
        $data = $schedule->toArray();

        if ($param->pattern === RecurrencePattern::CUSTOM->value) {
            $days = $param->days;
            if (empty($days)) {

                return;
            }
            asort($days);
            while (true) {
                foreach ($days as $day) {
                    $recDay = RecurrenceDay::from($day);
                    $currDate = Carbon::parse($currDate->format('Y-m-d H:i:s').' next '.$recDay->name);
                    if (! $currDate->lessThan($param->endDate)) {

                        return;
                    }

                    $this->createSchedule($data, $currDate, $dateIntervalInSeconds);
                }
            }
        } else {
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
                    default:
                        throw new \Exception('Invalid recurrence pattern.');
                }

                if (! $currDate->lessThan($param->endDate)) {

                    return;
                }

                $this->createSchedule($data, $currDate, $dateIntervalInSeconds);
            }
        }
    }

    private function createSchedule(array $data, Carbon $currDate, $dateIntervalInSeconds)
    {
        $data['starts_at'] = $currDate;
        $endDate = clone $currDate;
        $data['ends_at'] = $endDate->addSeconds($dateIntervalInSeconds);

        return Schedule::create($data);
    }
}
