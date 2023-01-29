<?php

namespace App\Http\Requests\V1\Admin\Category;

use App\Http\Requests\V1\FormRequest;

/**
 * Class GivePermissionRequest
 * @package App\Http\Requests\V1\Admin\Permissions
 */
class CategoryRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        return user()->isAdmin();
    }

    public array $rules = [
        'POST' => [
            'name' => 'required|string|max:50',
            'picture' => 'required_without:parent_id|file|mimes:jpeg,jpg,png|max:2048',
            'parent_id' => 'nullable|exists:categories,id|prohibits:picture',
        ],
        'PUT' => [
            'name' => 'nullable|string|max:50',
            'picture' => 'file|mimes:jpeg,jpg,png|max:2048',
            'parent_id' => 'nullable|exists:categories,id|prohibits:picture',
        ],
        'DELETE' => [
            'categories' => 'required|array|max:30',
            'categories.*' => 'required|exists:categories,id'
        ]
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array {
        return $this->rules[$this->getMethod()];
    }
}
