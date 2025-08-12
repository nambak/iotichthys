<?php

use App\Http\Requests\Category\CategoryRequest;
use Illuminate\Support\Facades\Validator;

test('CategoryRequest 유효성 검사 규칙이 올바르게 작동함', function () {
    $request = new CategoryRequest();
    $rules = $request->rules();
    
    // 필수 필드 검증
    $validator = Validator::make([], $rules, $request->messages());
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
    
    // 올바른 데이터 검증
    $validData = [
        'name' => 'Test Category',
        'description' => 'Test Description',
        'parent_id' => null,
        'sort_order' => 0,
        'is_active' => true,
    ];
    
    $validator = Validator::make($validData, $rules, $request->messages());
    expect($validator->passes())->toBeTrue();
});

test('CategoryRequest 사용자 정의 에러 메시지가 한글로 표시됨', function () {
    $request = new CategoryRequest();
    $rules = $request->rules();
    $messages = $request->messages();
    
    // 빈 이름으로 검증 실패
    $validator = Validator::make(['name' => ''], $rules, $messages);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('카테고리 이름을 입력해주세요.');
    
    // 너무 긴 이름
    $validator = Validator::make(['name' => str_repeat('a', 256)], $rules, $messages);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('카테고리 이름은 255자를 초과할 수 없습니다.');
    
    // 너무 긴 설명
    $validator = Validator::make([
        'name' => 'Test',
        'description' => str_repeat('a', 1001)
    ], $rules, $messages);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('description'))->toBe('설명은 1000자를 초과할 수 없습니다.');
});

test('CategoryRequest가 올바른 validation 규칙을 가짐', function () {
    $request = new CategoryRequest();
    $rules = $request->rules();
    
    expect($rules)->toHaveKey('name');
    expect($rules)->toHaveKey('description');
    expect($rules)->toHaveKey('parent_id');
    expect($rules)->toHaveKey('sort_order');
    expect($rules)->toHaveKey('is_active');
    
    expect($rules['name'])->toContain('required');
    expect($rules['name'])->toContain('string');
    expect($rules['name'])->toContain('max:255');
    
    expect($rules['description'])->toContain('nullable');
    expect($rules['description'])->toContain('string');
    expect($rules['description'])->toContain('max:1000');
});