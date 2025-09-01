<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;


class CorrectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "clock_in_time_after" => "required|date_format:H:i",
            "clock_out_time_after" => "required|date_format:H:i",
            "rests_after.*.start" => "nullable|date_format:H:i",
            "rests_after.*.end" => "nullable|date_format:H:i",
            "notes_after" => "required",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            $clockIn = isset($data['clock_in_time_after']) ? Carbon::createFromFormat('H:i', $data['clock_in_time_after']) : null;
            $clockOut = isset($data['clock_out_time_after']) ? Carbon::createFromFormat('H:i', $data['clock_out_time_after']) : null;

            if ($clockIn && $clockOut && $clockIn->gte($clockOut)) {
                $validator->errors()->add('clock_in_time_after', '出勤時間もしくは退勤時間が不適切な値です。');
            }

            $validRests = [];
            foreach ($data['rests_after'] ?? [] as $index => $restData) {
                $breakStart = isset($restData['start']) ? Carbon::createFromFormat('H:i', $restData['start']) : null;
                $breakEnd = isset($restData['end']) ? Carbon::createFromFormat('H:i', $restData['end']) : null;

                if ($breakStart && $breakEnd && $breakStart->gte($breakEnd)) {
                    $validator->errors()->add("rests_after.{$index}.start", '休憩時間が不適切な値です。');
                }

                if ($breakStart && $clockIn && $clockOut && ($breakStart->lt($clockIn) || $breakStart->gt($clockOut))) {
                    $validator->errors()->add("rests_after.{$index}.start", '休憩時間が不適切な値です。');
                }

                if ($breakEnd && $clockIn && $clockOut && ($breakEnd->lt($clockIn) || $breakEnd->gt($clockOut))) {
                    $validator->errors()->add("rests_after.{$index}.end", '休憩時間もしくは退勤時間が不適切な値です。');
                }

                if ($breakStart && $breakEnd) {
                    $validRests[] = [
                        'start' => $breakStart,
                        'end' => $breakEnd,
                        'index' => $index,
                    ];
                }
            }

            if (count($validRests) > 1) {
                usort($validRests, function ($a, $b) {
                    return $a['start']->gt($b['start']);
                });

                for ($i = 1; $i < count($validRests); $i++) {
                    $previousRest = $validRests[$i - 1];
                    $currentRest = $validRests[$i];

                    if ($currentRest['start']->lt($previousRest['end'])) {
                        $validator->errors()->add("rests_after.{$currentRest['index']}.start", '休憩時間が前の休憩時間と重複しています。');
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            "clock_in_time_after.required" => "出勤時間もしくは退勤時間が不適切な値です。",
            "clock_out_time_before.required" => "出勤時間もしくは退勤時間が不適切な値です。",
            "notes_after.required" => "備考を記入してください。",
        ];
    }
}