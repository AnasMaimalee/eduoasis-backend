<?php
namespace App\Http\Requests\CBT\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCbtSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'subjects_count' => (int) $this->subjects_count,
            'questions_per_subject' => (int) $this->questions_per_subject,
            'duration_minutes' => (int) $this->duration_minutes,
            'exam_fee' => (float) $this->exam_fee,
        ]);
    }

    public function rules(): array
    {
        return [
            'subjects_count' => 'required|integer|min:1|max:20',
            'questions_per_subject' => 'required|integer|min:5|max:50',
            'duration_minutes' => 'required|integer|min:30|max:300',
            'exam_fee' => 'required|numeric|min:0|max:10000',
        ];
    }
}
