<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRvuValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $hospitalId = hospital('id');
        
        return [
            'cost_reference_id' => [
                'required',
                'integer',
                Rule::exists('cost_references', 'id')->where('hospital_id', $hospitalId),
            ],
            'cost_center_id' => [
                'required',
                'integer',
                Rule::exists('cost_centers', 'id')->where('hospital_id', $hospitalId),
            ],
            'period_year' => 'required|integer|min:2020|max:2100',
            'period_month' => 'nullable|integer|min:1|max:12',
            'time_factor' => 'required|integer|min:1',
            'professionalism_factor' => 'required|integer|in:1,2,3,4,5',
            'difficulty_factor' => 'required|integer|min:1|max:10',
            'normalization_factor' => 'nullable|numeric|min:0.1|max:10.0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'cost_reference_id.required' => 'Cost Reference harus dipilih.',
            'cost_reference_id.exists' => 'Cost Reference tidak ditemukan.',
            'cost_center_id.required' => 'Cost Center harus dipilih.',
            'cost_center_id.exists' => 'Cost Center tidak ditemukan.',
            'period_year.required' => 'Tahun periode harus diisi.',
            'period_year.min' => 'Tahun periode minimal 2020.',
            'period_year.max' => 'Tahun periode maksimal 2100.',
            'period_month.min' => 'Bulan periode harus antara 1-12.',
            'period_month.max' => 'Bulan periode harus antara 1-12.',
            'time_factor.required' => 'Waktu (menit) harus diisi.',
            'time_factor.min' => 'Waktu minimal 1 menit.',
            'professionalism_factor.required' => 'Profesionalisme harus dipilih.',
            'professionalism_factor.in' => 'Profesionalisme harus antara 1-5.',
            'difficulty_factor.required' => 'Tingkat Kesulitan harus diisi.',
            'difficulty_factor.min' => 'Tingkat Kesulitan minimal 1.',
            'difficulty_factor.max' => 'Tingkat Kesulitan maksimal 10.',
            'normalization_factor.min' => 'Faktor Normalisasi minimal 0.1.',
            'normalization_factor.max' => 'Faktor Normalisasi maksimal 10.0.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default normalization_factor to 1.0 if not provided
        if (!$this->has('normalization_factor') || $this->normalization_factor === null) {
            $this->merge(['normalization_factor' => 1.0]);
        }
        
        // Set default is_active to true if not provided
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
