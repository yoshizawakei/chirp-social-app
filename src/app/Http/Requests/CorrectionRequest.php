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

            // Carbonインスタンスに変換
            $clockIn = isset($data['clock_in_time_after']) ? Carbon::createFromFormat('H:i', $data['clock_in_time_after']) : null;
            $clockOut = isset($data['clock_out_time_after']) ? Carbon::createFromFormat('H:i', $data['clock_out_time_after']) : null;

            // 1. 出勤時間と退勤時間のチェック
            if ($clockIn && $clockOut && $clockIn->gte($clockOut)) {
                $validator->errors()->add('clock_in_time_after', '出勤時間もしくは退勤時間が不適切な値です。');
            }

            foreach ($data['rests_after'] ?? [] as $index => $restData) {
                $breakStart = isset($restData['start']) ? Carbon::createFromFormat('H:i', $restData['start']) : null;
                $breakEnd = isset($restData['end']) ? Carbon::createFromFormat('H:i', $restData['end']) : null;

                // 休憩時間の整合性チェック
                if ($breakStart && $breakEnd && $breakStart->gte($breakEnd)) {
                    $validator->errors()->add("rests_after.{$index}.start", '休憩時間が不適切な値です。');
                }

                // 2. 休憩開始時間と出勤時間・退勤時間のチェック
                if ($breakStart && $clockIn && $clockOut && ($breakStart->lt($clockIn) || $breakStart->gt($clockOut))) {
                    $validator->errors()->add("rests_after.{$index}.start", '休憩時間が不適切な値です。');
                }

                // 3. 休憩終了時間と出勤時間・退勤時間のチェック
                if ($breakEnd && $clockIn && $clockOut && ($breakEnd->lt($clockIn) || $breakEnd->gt($clockOut))) {
                    $validator->errors()->add("rests_after.{$index}.end", '休憩時間もしくは退勤時間が不適切な値です。');
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