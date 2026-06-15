<?php

namespace App\Http\Requests\Event;

use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $event = $this->route('event') instanceof Event
            ? $this->route('event')
            : Event::find($this->route('event'));

        if (! $event) {
            return false;
        }

        return $event->organizer_id === $this->user()->id;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $event = $this->route('event') instanceof Event
            ? $this->route('event')
            : Event::find($this->route('event'));

        $mergeData = [];

        $totalPointPool = $this->input('total_point_pool') ?? $this->input('total_event_point') ?? $this->input('point_pool') ?? ($event ? $event->total_point_pool : null);

        if ($totalPointPool !== null) {
            $mergeData['total_point_pool'] = (int) $totalPointPool;
            $mergeData['total_event_point'] = (int) $totalPointPool;
            $mergeData['point_pool'] = (int) $totalPointPool;
        }

        if (! $this->has('max_points') && $totalPointPool !== null) {
            $mergeData['max_points'] = (int) $totalPointPool;
        }

        if (! empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $event = $this->route('event') instanceof Event
            ? $this->route('event')
            : Event::find($this->route('event'));

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'total_rewards' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'in:draft,published,ongoing,finished'],
            'total_point_pool' => ['required', 'integer', 'min:1'],
            'total_event_point' => ['required', 'integer', 'min:1'],
            'point_pool' => ['required', 'integer', 'min:1'],
            'max_points' => ['required', 'integer', 'min:1'],
        ];

        if ($event) {
            $currentStatus = strtolower($event->getRawOriginal('status') ?? 'draft');
            if (in_array($currentStatus, ['ongoing', 'finished'])) {
                $rules['total_point_pool'][] = function ($attribute, $value, $fail) use ($event) {
                    if ((int) $value !== (int) $event->total_point_pool) {
                        $fail('Total point pool tidak dapat diubah karena status event sudah berlangsung atau selesai.');
                    }
                };
            }

            $targetStatus = strtolower($this->input('status') ?? '');
            if (in_array($targetStatus, ['published', 'ongoing']) && $currentStatus !== $targetStatus) {
                $rules['status'][] = function ($attribute, $value, $fail) use ($event) {
                    $totalCP = $event->checkpoints()->count();
                    if ($totalCP === 0) {
                        $fail('Event harus memiliki minimal 1 checkpoint sebelum dipublikasikan atau dimulai.');
                    }

                    $sum = (int) $event->checkpoints()->sum('point');
                    if ($sum !== (int) $event->total_point_pool) {
                        $fail('Jumlah poin seluruh checkpoint ('.number_format($sum).') harus sama dengan total point pool event ('.number_format($event->total_point_pool).').');
                    }
                };
            }
        }

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
            'status.required' => 'Status event wajib diisi.',
            'status.in' => 'Status event tidak valid.',
            'point_pool.required' => 'Total point pool wajib diisi.',
            'point_pool.min' => 'Total point pool minimal 1.',
            'banner.image' => 'File banner harus berupa gambar.',
            'banner.mimes' => 'Format banner harus jpeg, png, jpg, atau webp.',
            'banner.max' => 'Ukuran banner tidak boleh lebih dari 2MB.',
            'total_event_point.required' => 'Total poin event wajib diisi.',
            'total_event_point.min' => 'Total poin event minimal 0.',
            'point_distribution_mode.required' => 'Mode distribusi poin wajib diisi.',
            'point_distribution_mode.in' => 'Mode distribusi poin tidak valid.',
            'fastest_participant_limit.required' => 'Batas peserta tercepat wajib diisi.',
            'fastest_participant_limit.min' => 'Batas peserta tercepat minimal 0.',
            'bonus_percentage.required' => 'Persentase bonus wajib diisi.',
            'bonus_percentage.min' => 'Persentase bonus minimal 0.',
            'bonus_percentage.max' => 'Persentase bonus maksimal 100.',
        ];
    }
}
