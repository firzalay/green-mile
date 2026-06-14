<?php

namespace App\Http\Requests\Checkpoint;

use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCheckpointRequest extends FormRequest
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
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sequence' => ['required', 'integer', 'min:1'],
            'points' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:active,inactive,Active,Inactive'],
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
            'name.required' => 'Nama checkpoint wajib diisi.',
            'name.max' => 'Nama checkpoint tidak boleh lebih dari 255 karakter.',
            'sequence.required' => 'Urutan checkpoint wajib diisi.',
            'sequence.min' => 'Urutan checkpoint minimal 1.',
            'points.required' => 'Poin checkpoint wajib diisi.',
            'points.min' => 'Poin checkpoint minimal 1.',
            'status.required' => 'Status checkpoint wajib diisi.',
            'status.in' => 'Status checkpoint tidak valid.',
        ];
    }
}
