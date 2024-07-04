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
        $hour = $currDate->hour;
        $minute = $currDate->minuteOfHour;
        $second = $currDate->second;
        $dateIntervalInSeconds = $currDate->diffInSeconds($schedule->ends_at);
        $data = $schedule->toArray();

        if ($param->pattern === RecurrencePattern::CUSTOM->value) {
            $days = $param->days;
            if (empty($days)) {

                return;
            }
            $days = $this->getOrganizedDays($days, $currDate->dayName);

            while (true) {
                foreach ($days as $day) {
                    $recDay = RecurrenceDay::from($day);
                    // When carbon parses to next matched day it won't match the H:i:s, so we should set it manually
                    $currDate = Carbon::parse($currDate->format('Y-m-d').' next '.$recDay->name);
                    $currDate->setHour($hour);
                    $currDate->setMinute($minute);
                    $currDate->setSecond($second);

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

    private function getOrganizedDays(array $days, $currentDayName): array
    {
        asort($days);
        $enumDays = get_enum_values(RecurrenceDay::cases(), true);
        $currentDayName = strtoupper($currentDayName);
        $todayNum = $enumDays[$currentDayName];

        // Organize days for correct functionality
        foreach ($days as $key => $day) {
            if ($day > $todayNum) {
                $slicedDays = array_slice($days, $key - 1, count($days));
                $days = array_merge($slicedDays, array_slice($days, 0, $key));

                break;
            }
        }

        return $days;
    }
}
