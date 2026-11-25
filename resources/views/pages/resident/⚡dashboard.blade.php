<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    #[Computed]
    public function requests()
    {
        return auth()->user()
            ->maintenanceRequests()
            ->latest()
            ->get();
    }

    #[Computed]
    public function totalRequests()
    {
        return auth()->user()->maintenanceRequests()->count();
    }

    #[Computed]
    public function pendingRequests()
    {
        return auth()->user()->maintenanceRequests()
            ->where('status', 'pending')
            ->count();
    }

    #[Computed]
    public function completedRequests()
    {
        return auth()->user()->maintenanceRequests()
            ->where('status', 'completed')
            ->count();
    }
};
?>

<div class="p-6 space-y-6">

    <div>
        <h1 class="text-2xl font-bold">Resident Dashboard</h1>
        <p class="text-gray-500">
            Welcome back, {{ auth()->user()->name }}!
            Manage your hostel maintenance requests here.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">

        <div class="rounded-xl border p-6">
            <h2 class="text-sm text-gray-500">Total Requests</h2>
            <p class="mt-2 text-3xl font-bold">
                {{ $this->totalRequests }}
            </p>
        </div>

        <div class="rounded-xl border p-6">
            <h2 class="text-sm text-gray-500">Pending</h2>
            <p class="mt-2 text-3xl font-bold">
                {{ $this->pendingRequests }}
            </p>
        </div>

        <div class="rounded-xl border p-6">
            <h2 class="text-sm text-gray-500">Completed</h2>
            <p class="mt-2 text-3xl font-bold">
                {{ $this->completedRequests }}
            </p>
        </div>

    </div>

    <div class="rounded-xl border p-6 space-y-6">

      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
    <h2 class="text-lg font-semibold">My Maintenance Requests</h2>

    <a href="{{ route('resident.requests.create') }}"
       style="display:inline-block; background-color:black; color:white; padding:10px 20px; border-radius:8px; text-decoration:none;">
        + New Request
    </a>
</div>

        @forelse ($this->requests as $request)

            <div class="rounded-lg border p-6 space-y-4">
            <a href="{{ route('resident.requests.show', $request->id) }}"
                style="display:block; border:1px solid #ddd; border-radius:12px; padding:24px; margin-bottom:16px; color:inherit; text-decoration:none;">
                <h3 class="font-semibold">
                    {{ $request->title }}
                </h3>

                <p class="text-sm text-gray-500">
                    {{ $request->category }} |
                    {{ $request->location }}
                </p>

                <p class="text-sm">
                    {{ $request->description }}
                </p>

                <span class="inline-block rounded-full bg-yellow-100 px-3 py-1 text-sm text-yellow-800">
                    {{ ucfirst($request->status) }}
                </span>
            </a>
            </div>

        @empty

            <p class="text-sm text-gray-500">
                You haven't submitted any maintenance requests yet.
            </p>

        @endforelse

    </div>

</div>
