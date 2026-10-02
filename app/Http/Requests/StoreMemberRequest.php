<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dibatasi middleware role di route
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', 'unique:members,email'],

            'plates'   => ['required', 'array', 'min:1'],
            'plates.*' => ['required', 'string', 'max:20', 'distinct', 'unique:vehicles,plate_number'],

            'vehicle_types'   => ['required', 'array', 'min:1'],
            'vehicle_types.*' => ['required', 'in:mobil,motor'],

            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'plates.*.unique'    => 'Plat nomor :input sudah terdaftar.',
            'plates.*.distinct'  => 'Ada plat nomor yang ditulis dobel.',
            'photo.required'     => 'Foto wajah wajib diunggah.',
            'photo.image'        => 'File harus berupa gambar.',
        ];
    }
}