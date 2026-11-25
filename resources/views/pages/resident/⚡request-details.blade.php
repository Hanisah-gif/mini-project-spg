<?php

use Livewire\Component;
use App\Models\MaintenanceRequest;

new class extends Component
{
    public MaintenanceRequest $request;

    public function mount(MaintenanceRequest $request)
    {
       $this->authorize('view', $request);

        $this->request = $request;
    }

    public function delete()
    {
        $this->authorize('delete', $this->request);
        abort_unless($this->request->status === 'pending', 403);

        $this->request->delete();

        session()->flash('success', 'Maintenance request deleted successfully!');

        return $this->redirect(route('resident.dashboard'));
    }
};
?>

<div class="p-6 space-y-6 max-w-3xl">
    <div>
        <h1 class="text-2xl font-bold">Request Details</h1>
        <p class="text-gray-500">
            View your maintenance request information.
        </p>
    </div>

    <div class="rounded-xl border p-6 space-y-5">
        <div>
            <h2 class="text-sm text-gray-500">Request Title</h2>
            <p class="text-lg font-semibold">{{ $request->title }}</p>
        </div>

        <div>
            <h2 class="text-sm text-gray-500">Category</h2>
            <p>{{ $request->category }}</p>
        </div>

        <div>
            <h2 class="text-sm text-gray-500">Location</h2>
            <p>{{ $request->location }}</p>
        </div>

        <div>
            <h2 class="text-sm text-gray-500">Description</h2>
            <p>{{ $request->description }}</p>
        </div>

        <div>
            <h2 class="text-sm text-gray-500">Status</h2>
            <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-sm text-yellow-800">
                {{ ucfirst($request->status) }}
            </span>
        </div>

       @if ($request->status === 'pending')
    <div style="display:flex; gap:12px; flex-wrap:wrap;">

        <a href="{{ route('resident.requests.edit', $request->getKey()) }}"
           style="display:inline-block; background-color:black; color:white; padding:10px 20px; border-radius:8px;">
            Edit Request
        </a>

        <button
            type="button"
            wire:click="delete"
            wire:confirm="Are you sure you want to delete this maintenance request?"
            style="background-color:#dc2626; color:white; padding:10px 20px; border-radius:8px;">
            Delete Request
        </button>

    </div>
@endif

        <div>
            <h2 class="text-sm text-gray-500">Submitted On</h2>
            <p>{{ $request->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <div>
            <h2 class="text-sm text-gray-500">Admin Remarks</h2>
            <p>{{ $request->admin_remarks ?? 'No remarks yet.' }}</p>
        </div>

        <div>
            <h2 class="text-sm text-gray-500">Technician Remarks</h2>
            <p>{{ $request->technician_remarks ?? 'No updates yet.' }}</p>
        </div>

        <a href="{{ route('resident.dashboard') }}"
           style="display:inline-block; background-color:black; color:white; padding:10px 20px; border-radius:8px;">
            Back to Dashboard
        </a>
    </div>
</div>