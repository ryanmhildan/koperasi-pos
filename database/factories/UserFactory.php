<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nrp'           => $this->faker->unique()->numerify('####'),
            'username'      => $this->faker->userName,
            'password_hash' => Hash::make('password'),
            'email'         => $this->faker->unique()->safeEmail,
            'full_name'     => $this->faker->name, // 🔥 gunakan full_name
            'phone'         => $this->faker->phoneNumber,
            'join_date'     => now()->toDateString(),
            'is_active'     => true,
        ];
    }
}
