<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'notes'                  => 'nullable|string',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'items.required'                  => 'Daftar item pesanan wajib diisi.',
            'items.array'                     => 'Item pesanan harus berupa array.',
            'items.min'                       => 'Minimal harus ada 1 item dalam pesanan.',
            'items.*.product_id.required'     => 'ID produk wajib diisi.',
            'items.*.product_id.exists'       => 'Produk tidak ditemukan.',
            'items.*.quantity.required'       => 'Jumlah item wajib diisi.',
            'items.*.quantity.integer'        => 'Jumlah item harus berupa bilangan bulat.',
            'items.*.quantity.min'            => 'Jumlah item minimal 1.',
        ];
    }
}
