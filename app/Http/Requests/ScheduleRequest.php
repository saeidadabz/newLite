<?php

namespace App\Http\Requests;

use App\Enums\AvailabilityTypes;
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
        $types = get_enum_values(AvailabilityTypes::cases());

        return [
            'calendar_id'       => 'required|exists:calendars,id',
            'availability_type' => ['required', Rule::in($types)],
            'starts_at'         => ['required', 'date', 'date_format:'.Constants::SCHEDULE_DATE_FORMAT],
            'ends_at'           => ['required', 'date', 'date_format:'.Constants::SCHEDULE_DATE_FORMAT, 'after:starts_at'],
        ];
    }
}
