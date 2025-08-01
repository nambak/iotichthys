<?php

namespace App\Livewire\Organization;

use App\Http\Requests\Organization\OrganizationRequest;
use App\Models\Organization;
use Livewire\Component;

class CreateModal extends Component
{
    public string $name = '';
    public string $owner = '';
    public string $postcode = '';
    public string $address = '';
    public string $detail_address = '';
    public string $phone_number = '';
    public string $business_register_number = '';

    /**
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
        // Form Request 클래스에서 validation rules와 messages 가져오기
        $request = new OrganizationRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        Organization::create($validatedData);

        $this->modal('create-organization')->close();

        $this->dispatch('organization-created');

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset();

        $this->resetValidation();
    }
}
