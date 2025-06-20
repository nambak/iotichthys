<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                   => ['required'],
            'owner'                  => ['required', 'min:2'],
            'address'                => ['required', 'min:10'],
            'phoneNumber'            => ['required', 'starts_with:0', 'digits_between:9,10'],
            'businessRegisterNumber' => ['required', 'digits:10', 'unique:organizations,business_register_number'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'                   => '조직이름을 입력해 주세요',
            'owner.min'                       => '최소 2글자 이상 입력해 주세요',
            'owner.required'                  => '대표자명을 입력해 주세요',
            'address.min'                     => '주소를 자세히 입력해 주세요',
            'address.required'                => '사업장 주소를 입력해 주세요',
            'phoneNumber.required'            => '사업자 전화번호를 입력해 주세요',
            'phoneNumber.starts_with'         => '전화번호를 다시 확인해 주세요',
            'phoneNumber.digits_between'      => '전화번호를 다시 확인해 주세요',
            'businessRegisterNumber.digits'   => '10자리 사업자 번호를 입력해 주세요',
            'businessRegisterNumber.unique'   => '이미 등록된 사업자 번호입니다',
            'businessRegisterNumber.required' => '사업자 번호를 입력해 주세요',
        ];
    }
}
