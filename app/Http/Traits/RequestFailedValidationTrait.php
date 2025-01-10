<?php

namespace App\Http\Traits;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

trait RequestFailedValidationTrait
{
    /**
     * @param  Validator  $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = [
            "error_code" => 1,
            "error_msg" => $this->getErrorMessage($validator),
            "fields" => $validator->errors()
        ];

        throw new HttpResponseException(response()->json(['error' => $response], 422));
    }

    /**
     * Сообщение для общей ошибки
     * @return string
     */
    public function getMessage()
    {
        if (isset($this->messageFailedValidation)) {
            return $this->messageFailedValidation;
        } else {
            if (request()->route()->getActionMethod() === 'login') {
                return __('validation.auth');
            }
            return __('validation.message');
        }
    }

    public function getErrorMessage(Validator $validator): string
    {
        if (request()->route()->getActionMethod() === 'forgot') {
            return $validator->errors()->toArray()['email'][0];
        }

        if (in_array(request()->route()->getName(), ['file.upload', 'image.upload'])) {
            return $validator->errors()->toArray()['file'][0];
        }

        return $this->getMessage();
    }
}
