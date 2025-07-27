<?php

namespace App\Livewire\Organization;

use App\Http\Requests\UpdateOrganizationRequest;
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
    public function save()
    {
        if (!$this->organization) {
            return;
        }

        //  TODO: 권한 체크

        $request = new UpdateOrganizationRequest();

        request()->merge(['organization_id' => $this->organization->id]);

        try {
            $validatedData = $this->validate($request->rules(), $request->messages());
            \Log::info('EditModal: Validation passed', $validatedData);

            $this->organization->update($validatedData);
            \Log::info('EditModal: Organization updated successfully');

            $this->modal('edit-organization')->close();
            \Log::info('EditModal: Modal closed');

            // 이벤트 발생 전 로그
            \Log::info('EditModal: About to dispatch organization-updated event');
            $this->dispatch('organization-updated');
            \Log::info('EditModal: organization-updated event dispatched');

        } catch (\Exception $e) {
            \Log::error('EditModal: Error in save method', ['error' => $e->getMessage()]);
            session()->flash('error', 'An error occurred while updating the organization.');
        }
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