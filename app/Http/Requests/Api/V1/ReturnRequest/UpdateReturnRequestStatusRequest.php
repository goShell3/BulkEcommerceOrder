<?php

namespace App\Http\Requests\Api\V1\ReturnRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReturnRequestStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:approved,rejected,completed'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Return request status is required.',
            'status.in' => 'Invalid return request status. Allowed values are: approved, rejected, completed.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(
            function ($validator) {
                $returnRequest = $this->route('returnRequest');
            
                if ($returnRequest && !$returnRequest->canBeUpdatedTo($this->status)) {
                    $validator->errors()->add('status', 'This return request cannot be updated to the specified status.');
                }
            }
        );
    }
} 
