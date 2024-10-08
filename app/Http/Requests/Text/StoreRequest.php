<?php

namespace App\Http\Requests\Text;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['string'],
            'text' => ['required', 'string', 'min:1'],
            'tags' => ['array'],
            'is_public' => ['boolean'],
            'expiration' => ['required', 'integer', 'min:0', 'max:527040'],
        ];
    }
}
