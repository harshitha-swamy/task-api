<?php
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title'            => ['sometimes', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'status'           => ['sometimes', 'in:pending,in_progress,completed,cancelled'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'due_date'         => ['nullable', 'date'],
        ];
    }
}