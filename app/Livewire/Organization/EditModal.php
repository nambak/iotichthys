<?php

namespace App\Livewire\Organization;

use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Contracts\View\Factory;
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
     * @return Factory|
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
     * @param  int  $organizationId
     */
    #[On('open-edit-organization')]
    public function openEdit($organizationId): void
    {
        $organization = Organization::findOrFail($organizationId);

        // TODO: 권한 체크

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
    public function update()
    {
        if (! $this->organization) {
            return;
        }

        //  TODO: 권한 체크

        $request = new UpdateOrganizationRequest;

        request()->merge(['organization_id' => $this->organization->id]);

        $validatedData = $this->validate($request->rules(), $request->messages());

        $this->organization->update($validatedData);

        $this->modal('edit-organization')->close();

        $this->dispatch('organization-updated');

        $this->resetForm();
    }

    /**
     * 폼 리셋
     *
     * @return void
     */
    public function resetForm()
    {
        $this->resetValidation();
    }
}
