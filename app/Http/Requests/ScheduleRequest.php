<?php

namespace App\Http\Requests;

use App\Enums\AvailabilityType;
use App\Enums\Days;
use App\Enums\RecurrenceDay;
use App\Enums\RecurrencePattern;
use App\Utilities\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    //TODO: validate for is_recurrence and ...
    public function rules(): array
    {
        $types = get_enum_values(AvailabilityType::cases());
//        $patterns = get_enum_values(RecurrencePattern::cases());
        $days = get_enum_values(Days::cases());

        return [
//            'calendar_id' => 'required|exists:calendars,id',

            "availability_type" => "required|array",
            "availability_type.*" => Rule::in($types),
            "days" => 'required|array',
            "days.*" => Rule::in($days),

//            'type' => ['required', Rule::in($types)],
//            'start_at' => ['required', 'time', 'date_format:' . Constants::SCHEDULE_DATE_FORMAT],
//            'end_at' => ['required', 'time', 'date_format:' . Constants::SCHEDULE_DATE_FORMAT, 'after:start_at'],
//            'recurrence_pattern'  => ['nullable', Rule::in($patterns)],
//            'recurrence_end_date' => ['required_with:recurrence_pattern', 'after:ends_at', 'date', 'date_format:'.Constants::SCHEDULE_DATE_FORMAT],
//            'recurrence_days'     => ['required_if:recurrence_pattern,'.RecurrencePattern::CUSTOM->value, 'array', Rule::in($days)],
        ];
    }
}
