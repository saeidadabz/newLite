<?php

namespace App\Http\Requests;

use App\Enums\AvailabilityType;
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
    public function rules(): array
    {
        $types = get_enum_values(AvailabilityType::cases());
        $patterns = get_enum_values(RecurrencePattern::cases());
        $days = get_enum_values(RecurrenceDay::cases());

        return [
            'calendar_id'         => 'required|exists:calendars,id',
            'availability_type'   => ['required', Rule::in($types)],
            'starts_at'           => ['required', 'date', 'date_format:'.Constants::SCHEDULE_DATE_FORMAT],
            'ends_at'             => ['required', 'date', 'date_format:'.Constants::SCHEDULE_DATE_FORMAT, 'after:starts_at'],
            'recurrence_pattern'  => ['nullable', Rule::in($patterns)],
            'recurrence_end_date' => ['required_with:recurrence_pattern', 'after:today', 'date', 'date_format:'.Constants::SCHEDULE_DATE_FORMAT],
            'recurrence_days'     => ['required_if:recurrence_pattern,'.RecurrencePattern::CUSTOM->value, Rule::in($days)],
        ];
    }
}
