<?php

namespace App\Livewire\Organization;

use App\Http\Requests\Organization\OrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Livewire\Attributes\On;
use Livewire\Component;

class Modal extends Component
{
    public $name = '';
    public $owner = '';
    public $postcode = '';
    public $address = '';
    public $detail_address = '';
    public $phone_number = '';
    public $business_register_number = '';
    public ?Organization $organization = null;

    public function mount(?Organization $organization = null)
    {
        if ($organization) {
            $this->organization = $organization;
            $this->fillFormFields();
        }
    }

    private function fillFormFields()
    {
        $this->name = $this->organization->name;
        $this->owner = $this->organization->owner;
        $this->postcode = $this->organization->postcode ?? '';
        $this->address = $this->organization->address;
        $this->detail_address = $this->organization->detail_address ?? '';
        $this->phone_number = $this->organization->phone_number;
        $this->business_register_number = $this->organization->business_register_number;
    }

    public function render()
    {
        return view('livewire.organization.modal', [
            'modalName' => 'organization-modal',
        ]);
    }

    /**
     * 조직 생성 모달 열기 (이벤트 리스너)
     */
    #[On('open-create-organization')]
    public function openCreate(): void
    {
        $this->initForCreate();
        $this->modal('organization-modal')->show();
    }

    /**
     * 조직 편집 모달 열기 (이벤트 리스너)
     */
    #[On('open-edit-organization')]
    public function openEdit($organizationId = null): void
    {
        if (is_null($organizationId)) {
            return;
        }

        $organization = Organization::findOrFail($organizationId);
        $this->organization = $organization;
        $this->fillFormFields();
        $this->resetValidation();
        $this->modal('organization-modal')->show();
    }

    /**
     * 조직 저장 (생성/수정)
     */
    public function save()
    {
        if ($this->isEditMode()) {
            $request = new UpdateOrganizationRequest;
            request()->merge(['organization_id' => $this->organization->id]);
            $validatedData = $this->validate($request->rules(), $request->messages());
            $this->organization->update($validatedData);
            $this->modal('organization-modal')->close();
            $this->dispatch('organization-updated');
        } else {
            $request = new OrganizationRequest;
            $validatedData = $this->validate($request->rules(), $request->messages());
            Organization::create($validatedData);
            $this->modal('organization-modal')->close();
            $this->dispatch('organization-created');
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->owner = '';
        $this->postcode = '';
        $this->address = '';
        $this->detail_address = '';
        $this->phone_number = '';
        $this->business_register_number = '';
        $this->organization = null;
        $this->resetValidation();
    }

    /**
     * 생성 모드로 모달 초기화
     */
    public function initForCreate()
    {
        $this->resetForm();
    }

    public function isEditMode(): bool
    {
        return $this->organization !== null;
    }
}
