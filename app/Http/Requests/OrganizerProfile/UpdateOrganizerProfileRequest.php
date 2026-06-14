<?php

namespace App\Http\Requests\OrganizerProfile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizerProfileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'username' => [
                'required',
                'alpha_num',
                'min:4',
                'max:255',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'organization_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'name.max' => 'Nama lengkap tidak boleh lebih dari 100 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_num' => 'Username hanya boleh berisi huruf dan angka.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.unique' => 'Username sudah digunakan.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'avatar.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'organization_name.required' => 'Nama organisasi wajib diisi.',
            'organization_name.max' => 'Nama organisasi tidak boleh lebih dari 255 karakter.',
            'contact_person.required' => 'Nama kontak person wajib diisi.',
            'contact_person.max' => 'Nama kontak person tidak boleh lebih dari 255 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 255 karakter.',
            'website.url' => 'Format website tidak valid.',
            'website.max' => 'Website tidak boleh lebih dari 255 karakter.',
        ];
    }
}
