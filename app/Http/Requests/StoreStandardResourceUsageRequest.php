<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStandardResourceUsageRequest extends FormRequest
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
            'service_id' => [
                'required',
                'integer',
                Rule::exists('cost_references', 'id')->where('hospital_id', $hospitalId),
            ],
            'service_name' => 'nullable|string|max:255',
            'service_code' => 'nullable|string|max:100',
            'category' => 'required|string|in:barang,tindakan_rj,tindakan_ri,laboratorium,radiologi,operasi,kamar',
            'bmhp_items' => 'required|array|min:1',
            'bmhp_items.*.bmhp_id' => [
                'required',
                'integer',
                Rule::exists('cost_references', 'id')->where('hospital_id', $hospitalId),
            ],
            'bmhp_items.*.quantity' => 'required|numeric|min:0.01',
            'bmhp_items.*.unit' => 'required|string|max:50',
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
            'service_id.required' => 'Service harus dipilih.',
            'service_id.exists' => 'Service tidak ditemukan.',
            'service_name.max' => 'Nama Service maksimal 255 karakter.',
            'category.required' => 'Category harus dipilih.',
            'category.in' => 'Category tidak valid.',
            'service_code.max' => 'Service Code maksimal 100 karakter.',
            'bmhp_items.required' => 'Minimal harus ada 1 BMHP.',
            'bmhp_items.min' => 'Minimal harus ada 1 BMHP.',
            'bmhp_items.*.bmhp_id.required' => 'BMHP harus dipilih.',
            'bmhp_items.*.bmhp_id.exists' => 'BMHP tidak ditemukan.',
            'bmhp_items.*.quantity.required' => 'Quantity harus diisi.',
            'bmhp_items.*.quantity.numeric' => 'Quantity harus berupa angka.',
            'bmhp_items.*.quantity.min' => 'Quantity minimal 0.01.',
            'bmhp_items.*.unit.required' => 'Unit harus diisi.',
            'bmhp_items.*.unit.max' => 'Unit maksimal 50 karakter.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hospitalId = hospital('id');
            $serviceId = $this->input('service_id');
            $bmhpItems = $this->input('bmhp_items', []);

            // Validasi: service_id tidak boleh sama dengan bmhp_id manapun
            if ($serviceId) {
                foreach ($bmhpItems as $index => $item) {
                    if (isset($item['bmhp_id']) && $item['bmhp_id'] == $serviceId) {
                        $validator->errors()->add("bmhp_items.{$index}.bmhp_id", 'BMHP tidak boleh sama dengan Service.');
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default is_active to true if not provided
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
