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

        if ($event->organizer_id !== $this->user()->id) {
            return false;
        }

        // Event Lock: No checkpoint changes on ongoing or finished events
        $status = strtolower($event->getRawOriginal('status') ?? 'draft');
        if (in_array($status, ['ongoing', 'finished'])) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $event = $this->route('event');
        if (! $event instanceof Event) {
            $event = Event::find($this->route('event'));
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sequence' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:active,inactive,Active,Inactive'],
            'is_custom_point' => ['nullable', 'boolean'],
            'bonus_tiers' => ['nullable', 'array'],
            'bonus_tiers.*.rank_start' => ['required_with:bonus_tiers', 'integer', 'min:1'],
            'bonus_tiers.*.rank_end' => ['nullable', 'integer', 'min:1', 'gte:bonus_tiers.*.rank_start'],
            'bonus_tiers.*.bonus_percentage' => ['required_with:bonus_tiers', 'numeric', 'min:0'],
        ];

        $rules['points'] = [
            'nullable',
            'integer',
            function ($attribute, $value, $fail) use ($event) {
                if ($this->boolean('is_custom_point') && ($value === null || $value === '')) {
                    $fail('Poin checkpoint wajib diisi jika kustomisasi poin diaktifkan.');

                    return;
                }

                if ($this->boolean('is_custom_point')) {
                    if ((int) $value < 1) {
                        $fail('Poin checkpoint minimal 1.');

                        return;
                    }

                    $allocated = (int) $event->checkpoints()->sum('point');
                    $limit = (int) $event->total_point_pool;
                    if ($allocated + (int) $value > $limit) {
                        $fail('Poin checkpoint melebihi total point pool event.');
                    }
                }
            },
        ];

        return $rules;
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
