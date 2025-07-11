<?php

return [
    'organization' => [
        'name'                   => [
            'require' => '사업자명을 입력해 주세요',
            'min'     => '사업자명은 최소 2글자 최대 30글자 입니다',
            'max'     => '사업자명은 최소 2글자 최대 30글자 입니다',
        ],
        'owner'                  => [
            'required' => '대표자명을 입력해 주세요',
            'min'      => '최소 2글자 이상 입력해 주세요',
        ],
        'address'                => [
            'required' => '사업장 주소를 입력해 주세요',
            'min'      => '주소를 자세히 입력해 주세요',
        ],
        'phoneNumber'            => [
            'required'       => '사업자 전화번호를 입력해 주세요',
            'numeric'        => '전화번호를 다시 확인해 주세요',
            'starts_with'    => '전화번호를 다시 확인해 주세요',
            'digits_between' => '전화번호를 다시 확인해 주세요',
        ],
        'businessRegisterNumber' => [
            'required' => '사업자번호를 입력해 주세요',
            'numeric'  => '사업자번호를 다시 확인해 주세요',
            'digits'   => '10자리 사업자번호를 입력해 주세요',
            'unique'   => '이미 등록된 사업자번호입니다',
        ],
    ],
];
