<?php

use Livewire\Component;
use App\Models\MaintenanceRequest;

new class extends Component
{
    public MaintenanceRequest $request;

    public $title = '';
    public $category = '';
    public $location = '';
    public $description = '';

    public function mount(MaintenanceRequest $request)
    {
        $this->authorize('update', $request);
        abort_unless($request->status === 'pending', 403);

        $this->request = $request;

        $this->title = $request->title;
        $this->category = $request->category;
        $this->location = $request->location;
        $this->description = $request->description;
    }

    public function update()
    {
        $this->authorize('update', $this->request);
        abort_unless($this->request->status === 'pending', 403);

        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $this->request->update($validated);

        session()->flash('success', 'Maintenance request updated successfully!');

        return $this->redirect(
            route('resident.requests.show', $this->request->getKey())
        );
    }
};
?>

<div class="p-6 space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-bold">Edit Maintenance Request</h1>
        <p class="text-gray-500">
            Update your pending maintenance request.
        </p>
    </div>

    <form wire:submit="update" class="space-y-5">

        <div>
            <label>Request Title</label>
            <input type="text" wire:model="title"
                class="w-full rounded-lg border p-3">
            @error('title') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Category</label>
            <input type="text" wire:model="category"
                class="w-full rounded-lg border p-3">
            @error('category') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Location</label>
            <input type="text" wire:model="location"
                class="w-full rounded-lg border p-3">
            @error('location') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label>Description</label>
            <textarea wire:model="description"
                class="w-full rounded-lg border p-3"
                rows="4"></textarea>
            @error('description') <p class="text-red-500">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
            style="background-color:black; color:white; padding:12px 24px; border-radius:8px;">
            Save Changes
        </button>

        <a href="{{ route('resident.requests.show', $request->getKey()) }}"
            style="padding:12px 20px;">
            Cancel
        </a>
    </form>
</div>