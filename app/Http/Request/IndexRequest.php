<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'classId' => ['required', 'string'],
            'keyWork' => ['nullable'],
        ];
    }
}