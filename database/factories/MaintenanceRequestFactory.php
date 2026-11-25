<?php

namespace Database\Factories;

use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceRequestFactory extends Factory
{
    protected $model = MaintenanceRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement([
                'Electrical',
                'Plumbing',
                'Furniture',
                'Cleaning',
                'Other',
            ]),
            'location' => fake()->randomElement([
                'A1-2-1',
                'A2-4-5',
                'A6-7-1',
            ]),
            'status' => 'pending',
            'technician_id' => null,
            'admin_remarks' => null,
            'technician_remarks' => null,
        ];
    }
}
