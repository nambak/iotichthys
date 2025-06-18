<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateModal extends Component
{
    #[Validate('required', message: '조직이름을 입력해 주세요')]
    #[Validate('min:1', message: '최소 1글자 이상 입력해 주세요')]
    public $name;

    #[Validate('required', message: '대표자명을 입력해 주세요')]
    #[Validate('min:2', message: '최소 2글자 이상 입력해 주세요')]
    public $owner;

    #[Validate('required', message: '사업자 번호를 입력해 주세요')]
    #[Validate('min:10', message: '10자리 사업자 번호를 입력해 주세요')]
    public $businessRegisterNumber;

    #[Validate('required', message: '사업장 주소를 입력해 주세요')]
    #[Validate('min:10', message: '주소를 자세히 입력해 주세요')]
    public $address;

    #[Validate('required', message: '사업자 전화번호를 입력해 주세요')]
    #[Validate('min:9', message: '전화번호를 다시 확인해 주세요')]
    public $phoneNumber;

    /**
     * 조직 생성 모달
     *
     * @return \Illuminate\Contracts\View\Factory|
     *         \Illuminate\Contracts\View\View|
     *         \Illuminate\Foundation\Application|
     *         object
     */
    public function render()
    {
        return view('livewire.organization.create-modal');
    }

    /**
     * 조직 생성
     *
     * @return void
     */
    public function save()
    {
        $validatedData = $this->validate();

        $organization = new Organization();

        $organization->name = $validatedData['name'];
        $organization->owner = $validatedData['owner'];
        $organization->address = $validatedData['address'];
        $organization->phone_number = $validatedData['phoneNumber'];
        $organization->business_register_number = $validatedData['businessRegisterNumber'];

        $organization->save();

        $this->modal('create-organization')->close();

        $this->dispatch('organization-created');
    }
}
