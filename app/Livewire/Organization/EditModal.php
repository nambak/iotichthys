<?php

namespace App\Livewire\Organization;

use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?Organization $organization = null;
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
        return view('livewire.organization.edit-modal');
    }

    /**
     * 조직 편집 모달 열기 (이벤트 리스너)
     *
     * @param int $organizationId
     * @return void
     */
    #[On('open-edit-organization')]
    public function openEdit($organizationId)
    {
        $organization = Organization::findOrFail($organizationId);
        $this->organization = $organization;
        $this->name = $organization->name;
        $this->owner = $organization->owner;
        $this->address = $organization->address;
        $this->detail_address = $organization->detail_address ?? '';
        $this->postcode = $organization->postcode ?? '';
        $this->phone_number = $organization->phone_number;
        $this->business_register_number = $organization->business_register_number;

        $this->resetValidation();
        $this->modal('edit-organization')->show();
    }

    /**
     * 조직 수정
     *
     * @return void
     */
    public function save()
    {
        if (!$this->organization) {
            return;
        }

        // Form Request를 사용한 validation (수정 시 unique 규칙 수정)
        $request = new OrganizationRequest();
        $rules = $request->rules();
        
        // 수정 시에는 현재 조직의 사업자번호는 unique 체크에서 제외
        $rules['business_register_number'] = [
            'required', 
            'numeric', 
            'digits:10', 
            'unique:organizations,business_register_number,' . $this->organization->id
        ];

        $validatedData = $this->validate($rules, $request->messages());

        $this->organization->update($validatedData);

        $this->modal('edit-organization')->close();

        $this->dispatch('organization-updated');
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->organization = null;
        $this->name = '';
        $this->owner = '';
        $this->address = '';
        $this->phone_number = '';
        $this->business_register_number = '';
        $this->postcode = '';
        $this->detail_address = '';
        $this->resetValidation();
    }
}