<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'total_rewards' => ['nullable', 'string', 'max:255'],
            'max_points' => ['required', 'integer', 'min:1'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'point_pool' => ['required', 'integer', 'min:1'],
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
            'name.required' => 'Nama event wajib diisi.',
            'name.max' => 'Nama event tidak boleh lebih dari 255 karakter.',
            'location.required' => 'Lokasi event wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'max_points.required' => 'Maksimal poin wajib diisi.',
            'max_points.min' => 'Maksimal poin minimal 1.',
            'point_pool.required' => 'Total point pool wajib diisi.',
            'point_pool.min' => 'Total point pool minimal 1.',
            'banner.image' => 'File banner harus berupa gambar.',
            'banner.mimes' => 'Format banner harus jpeg, png, jpg, atau webp.',
            'banner.max' => 'Ukuran banner tidak boleh lebih dari 2MB.',
        ];
    }
}
