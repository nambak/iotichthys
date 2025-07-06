<?php

namespace App\Livewire\Organization;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Livewire\Component;

class CreateModal extends Component
{
    public string $name;
    public string $owner;
    public string $address;
    public string $phoneNumber;
    public string $businessRegisterNumber;

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
        // Form Request를 사용한 validation
        $request = new OrganizationRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

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

    public function resetForm()
    {
        $this->name = '';
        $this->owner = '';
        $this->businessRegisterNumber = '';
        $this->address = '';
        $this->phoneNumber = '';
    }
}
