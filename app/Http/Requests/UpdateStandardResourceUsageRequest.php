<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\UnitOfMeasurement;

class UpdateStandardResourceUsageRequest extends FormRequest
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
        $hasUoms = UnitOfMeasurement::where('hospital_id', $hospitalId)->exists();
        
        $rules = [
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
            'is_active' => 'nullable|boolean',
        ];

        if ($hasUoms) {
            $rules['bmhp_items.*.unit_of_measurement_id'] = 'required|exists:units_of_measurement,id';
            // unit text menjadi optional atau hidden
            $rules['bmhp_items.*.unit'] = 'nullable|string|max:50';
        } else {
            $rules['bmhp_items.*.unit'] = 'required|string|max:50';
        }

        return $rules;
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
            'bmhp_items.*.quantity.min' => 'Quantity minimal 0.01.',
            'bmhp_items.*.unit.required' => 'Unit harus diisi.',
            'bmhp_items.*.unit.max' => 'Unit maksimal 50 karakter.',
            'bmhp_items.*.unit_of_measurement_id.required' => 'Satuan harus dipilih.',
            'bmhp_items.*.unit_of_measurement_id.exists' => 'Satuan tidak valid.',
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

            // Auto-fill legacy unit field if using UoM ID
            if ($this->has('bmhp_items')) {
                $items = $this->input('bmhp_items');
                $modified = false;
                foreach ($items as $key => $item) {
                    if (isset($item['unit_of_measurement_id']) && empty($item['unit'])) {
                        $uom = UnitOfMeasurement::find($item['unit_of_measurement_id']);
                        if ($uom) {
                            $items[$key]['unit'] = $uom->symbol ?? $uom->code;
                            $modified = true;
                        }
                    }
                }
                if ($modified) {
                    $this->merge(['bmhp_items' => $items]);
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
