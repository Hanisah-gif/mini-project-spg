<?php

namespace App\Actions;

use App\Models\MaintenanceRequest;

class CreateMaintenanceRequest
{
    public function execute(array $data): MaintenanceRequest
    {
        return MaintenanceRequest::create([
            ...$data,
            'status' => 'pending',
        ]);
    }
}
