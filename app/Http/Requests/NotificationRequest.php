<?php

namespace App\Http\Requests;

use App\Utilities\Constants;
use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
        return [
            'title'    => 'required|string|max:1000',
            'message'  => 'nullable|string',
            'sends_at' => ['nullable', 'date', 'date_format:'.Constants::BASE_DATE_FORMAT],
        ];
    }
}
