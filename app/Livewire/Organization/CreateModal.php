<?php

namespace App\Livewire\Organization;

use App\Http\Requests\OrganizationRequest;
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

    protected $listeners = ['addressSelected'];

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
        // Form Request를 사용한 validation
        $request = new OrganizationRequest();
        $validatedData = $this->validate($request->rules(), $request->messages());

        Organization::create($validatedData);

        $this->modal('create-organization')->close();

        $this->dispatch('organization-created');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'owner',
            'postcode',
            'address',
            'detail_address',
            'phone_number',
            'business_register_number'
        ]);

        $this->resetValidation();
    }

    public function addressSelected($data)
    {
        $this->postcode = $data['postcode'] ?? '';
        $this->address = $data['address'] ?? '';
    }
}
