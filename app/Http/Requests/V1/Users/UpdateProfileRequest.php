<?php

namespace App\Http\Requests\V1\Users;

use App\Http\Requests\V1\FormRequest;

/**
 * Class UpdateProfileRequest
 * @package App\Http\Requests\V1\Users
 */
class UpdateProfileRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'file' => 'mimes:jpeg,jpg,png,svg,gif,webp|nullable|max:2048',
            'city_id' => 'nullable|exists:cities,id',
            'first_name' => 'nullable|string|max:100|min:2',
            'last_name' => 'nullable|string|max:100|min:2',
        ];
    }
}
