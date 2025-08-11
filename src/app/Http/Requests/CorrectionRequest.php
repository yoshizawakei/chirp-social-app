<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


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
            "clock_in_time_after" => "required",
            "clock_out_time_before" => "required",
            "notes_after" => "required",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            // フォームから送られてきた各時間の値を取得
            $clockIn = $data['clock_in_time_after'] ?? null;
            $clockOut = $data['clock_out_time_after'] ?? null;
            $notes = $data['notes_after'] ?? null;
            $rests = $data['rests_after'] ?? [];

            // 出勤時間が退勤時間より後になっている場合、退勤時間が出勤時間より前になっている場合
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_in_time_after', '出勤時間もしくは退勤時間が不適切な値です');
            }

            foreach ($rests as $index => $rest) {
                $breakStart = $rest['start'] ?? null;
                $breakEnd = $rest['end'] ?? null;

                // 休憩開始時間が出勤時間より前になっている場合、退勤時間より後になっている場合
                if ($breakStart && $clockIn && $clockOut && ($breakStart < $clockIn || $breakStart > $clockOut)) {
                    $validator->errors()->add("rests_after.$index.start", '休憩時間が不適切な値です');
                }

                // 休憩終了時間が退勤時間より後になっている場合
                if ($breakEnd && $clockOut && ($breakEnd > $clockOut)) {
                    $validator->errors()->add("rests_after.$index.end", '休憩時間もしくは退勤時間が不適切な値です');
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
