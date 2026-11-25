<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Actions\CreateMaintenanceRequest;

new class extends Component
{
    public $title = '';
    public $description = '';
    public $category = '';
    public $location = '';


public function submit()
{
    
   $formRequest = new StoreMaintenanceRequest();

    $validated = $this->validate(
        $formRequest->rules(),
        $formRequest->messages()
    );

    app(CreateMaintenanceRequest::class)->execute([
        ...$validated,
        'user_id' => Auth::id(),
    ]);

    session()->flash('success', 'Maintenance request submitted successfully!');

    $this->redirect(route('resident.dashboard'));
}

};
?>

<div>
  <div class="p-6 space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-bold">Submit Maintenance Request</h1>
        <p class="text-gray-500">
            Describe the maintenance problem in your hostel.
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-100 p-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5">

        <div>
            <label class="block font-medium mb-2">Request Title</label>
            <input type="text" wire:model.live="title"
                class="w-full rounded-lg border p-3"
                placeholder="e.g. Broken ceiling fan">
            @error('title')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-2">Category</label>
            <select wire:model.live="category"
                class="w-full rounded-lg border p-3">
                <option value="">Select category</option>
                <option value="Electrical">Electrical</option>
                <option value="Plumbing">Plumbing</option>
                <option value="Furniture">Furniture</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Other">Other</option>
            </select>
            @error('category')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-2">Hostel Location</label>
            <input type="text" wire:model.live="location"
                class="w-full rounded-lg border p-3"
                placeholder="e.g. Block A, Room 203">
            @error('location')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-2">Description</label>
            <textarea wire:model.live="description" rows="5"
                class="w-full rounded-lg border p-3"
                placeholder="Explain the maintenance problem..."></textarea>
            @error('description')
                <p class="text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
    style="display: block; background-color: black; color: white; padding: 12px 24px; border-radius: 8px;">
    Submit Request
</button>

    </form>
</div>
</div>