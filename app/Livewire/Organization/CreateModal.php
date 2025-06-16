<?php

namespace App\Livewire\Organization;

use App\Models\Organization;
use Livewire\Component;

class CreateModal extends Component
{
    public $name;
    public $owner;
    public $businessRegisterNumber;
    public $address;
    public $phoneNumber;

    protected $rules = [
        'name'                   => 'required|min:1',
        'owner'                  => 'required|min:2',
        'businessRegisterNumber' => 'required|min:10',
        'address'                => 'required|min:10',
        'phoneNumber'            => 'required|min:9',
    ];

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
        // TODO: 권한 체크 - Organization 생성 정책 확인
        // $this->authorize('create', Organization::class);

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
