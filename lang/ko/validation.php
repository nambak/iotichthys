<?php

return [
    'organization' => [
        'name'                   => [
            'required' => '사업자명을 입력해 주세요',
            'min'      => '사업자명은 최소 2글자 최대 30글자 입니다',
            'max'      => '사업자명은 최소 2글자 최대 30글자 입니다',
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
        'postcode'               => [
            'required' => '우편번호를 입력해 주세요',
            'numeric'  => '5자리 숫자로된 우편번호를 입력해 주세요',
            'digits'   => '5자리 숫자로된 우편번호를 입력해 주세요',
        ],
        'detailAddress'          => [
            'max'      => '상세 주소는 255자 이하로 작성해 주세요',
            'required' => '상세 주소를 입력해 주세요',
        ],
        'email'                  => [
            'required' => '이메일을 입력해주세요',
            'email'    => '이메일 형식에 맞게 입력해주세요',
        ],
    ],
    'team'         => [
        'organization_id' => [
            'required' => '조직을 선택해 주세요',
            'exists'   => '유효하지 않은 조직입니다',
        ],
        'name'            => [
            'required' => '팀명을 입력해 주세요',
            'min'      => '팀명은 최소 2글자 이상 입력해 주세요',
            'max'      => '팀명은 최대 50글자까지 입력할 수 있습니다',
        ],
        'slug'            => [
            'required' => '팀 식별자를 입력해 주세요',
            'min'      => '팀 식별자는 최소 2글자 이상 입력해 주세요',
            'max'      => '팀 식별자는 최대 50글자까지 입력할 수 있습니다',
            'regex'    => '팀 식별자는 영어 소문자, 숫자, 하이픈(-)만 사용할 수 있습니다',
            'unique'   => '이미 사용 중인 팀 식별자입니다',
        ],
        'description'     => [
            'max' => '팀 설명은 최대 500글자까지 입력할 수 있습니다',
        ],
    ],
    'permission'   => [
        'name'     => [
            'required' => '권한 이름을 입력해 주세요',
        ],
        'resource' => [
            'required' => '리소스명을 입력해 주세요'
        ],
        'action'   => [
            'required' => '액션명을 입력해 주세요'
        ]
    ],
];
