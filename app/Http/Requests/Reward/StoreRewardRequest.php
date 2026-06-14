<?php

namespace App\Http\Requests\Reward;

use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRewardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $event = $this->route('event');

        if (! $event instanceof Event) {
            $event = Event::find($this->route('event'));
        }

        if (! $event) {
            return false;
        }

        return $event->organizer_id === $this->user()->id;
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
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'required_points' => ['required', 'integer', 'min:1'],
            'stock' => ['required', 'integer', 'min:0'],
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
            'name.required' => 'Nama reward wajib diisi.',
            'name.max' => 'Nama reward tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Deskripsi reward wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'required_points.required' => 'Poin yang dibutuhkan wajib diisi.',
            'required_points.min' => 'Poin yang dibutuhkan minimal 1.',
            'stock.required' => 'Stok reward wajib diisi.',
            'stock.min' => 'Stok reward minimal 0.',
        ];
    }
}
