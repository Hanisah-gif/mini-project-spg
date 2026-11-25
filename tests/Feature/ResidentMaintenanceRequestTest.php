<?php

use App\Models\MaintenanceRequest;
use App\Models\User;
use Livewire\Livewire;

// 1. Resident can view their own request
test('resident can view their own maintenance request', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
    ]);

    $this->actingAs($resident)
        ->get(route('resident.requests.show', $request->id))
        ->assertOk();
});

// 2. Resident cannot view another resident's request
test('resident cannot view another residents request', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $otherResident->id,
    ]);

    $this->actingAs($resident)
        ->get(route('resident.requests.show', $request->id))
        ->assertForbidden();
});

// 3. Resident can access the edit page for their pending request
test('resident can update their pending request', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'status' => 'pending',
    ]);

    $this->actingAs($resident)
        ->get(route('resident.requests.edit', $request->id))
        ->assertOk();
});

// 4. Resident cannot edit another resident's request
test('resident cannot edit another residents request', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $otherResident->id,
        'status' => 'pending',
    ]);

    $this->actingAs($resident)
        ->get(route('resident.requests.edit', $request->id))
        ->assertForbidden();
});

// 5. Resident cannot edit a completed request
test('resident cannot edit a completed request', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'status' => 'completed',
    ]);

    $this->actingAs($resident)
        ->get(route('resident.requests.edit', $request->id))
        ->assertForbidden();
});

// 6. Resident can submit a maintenance request
test('resident can submit a maintenance request', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    Livewire::actingAs($resident)
        ->test('pages::resident.create-request')
        ->set('title', 'Broken ceiling fan')
        ->set('description', 'The ceiling fan is not working.')
        ->set('category', 'Electrical')
        ->set('location', 'A1-2-1')
        ->call('submit')
        ->assertRedirect(route('resident.dashboard'));

    $this->assertDatabaseHas('maintenance_requests', [
        'user_id' => $resident->id,
        'title' => 'Broken ceiling fan',
        'status' => 'pending',
    ]);
});

// 7. Resident cannot submit an empty request
test('resident cannot submit an empty request', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    Livewire::actingAs($resident)
        ->test('pages::resident.create-request')
        ->call('submit')
        ->assertHasErrors([
            'title',
            'description',
            'category',
            'location',
        ]);
});

// 8. Resident can update their pending request details
test('resident can update their pending request details', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($resident)
        ->test('pages::resident.edit-request', [
            'request' => $request,
        ])
        ->set('title', 'Updated ceiling fan')
        ->call('update')
        ->assertRedirect(route('resident.requests.show', $request->id));

    $this->assertDatabaseHas('maintenance_requests', [
        'id' => $request->id,
        'title' => 'Updated ceiling fan',
    ]);
});

// 9. Resident can delete their pending request
test('resident can delete their pending request', function () {
    $resident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($resident)
        ->test('pages::resident.request-details', [
            'request' => $request,
        ])
        ->call('delete')
        ->assertRedirect(route('resident.dashboard'));

    $this->assertDatabaseMissing('maintenance_requests', [
        'id' => $request->id,
    ]);
});

// 10. Resident cannot edit an assigned request
test('resident cannot edit an assigned request', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $technician = User::factory()->create(['role' => 'technician']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'technician_id' => $technician->id,
        'status' => 'pending',
    ]);

    $this->actingAs($resident)
        ->get(route('resident.requests.edit', $request->id))
        ->assertForbidden();
});

// 11. Resident cannot delete an assigned request
test('resident cannot delete an assigned request', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $technician = User::factory()->create(['role' => 'technician']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'technician_id' => $technician->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($resident)
        ->test('pages::resident.request-details', [
            'request' => $request,
        ])
        ->call('delete')
        ->assertForbidden();

    $this->assertDatabaseHas('maintenance_requests', [
        'id' => $request->id,
    ]);
});

// 12. Resident cannot delete another resident's request
test('resident cannot delete another residents request', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);

    $request = MaintenanceRequest::factory()->create([
        'user_id' => $otherResident->id,
        'status' => 'pending',
    ]);

    expect($resident->can('delete', $request))->toBeFalse();

    $this->assertDatabaseHas('maintenance_requests', [
        'id' => $request->id,
    ]);
});

// 13. Resident dashboard only displays their own requests
test('resident dashboard only displays their own requests', function () {
    $resident = User::factory()->create(['role' => 'resident']);
    $otherResident = User::factory()->create(['role' => 'resident']);

    MaintenanceRequest::factory()->create([
        'user_id' => $resident->id,
        'title' => 'My broken fan',
    ]);

    MaintenanceRequest::factory()->create([
        'user_id' => $otherResident->id,
        'title' => 'Other resident broken pipe',
    ]);

    Livewire::actingAs($resident)
        ->test('pages::resident.dashboard')
        ->assertSee('My broken fan')
        ->assertDontSee('Other resident broken pipe');
});
