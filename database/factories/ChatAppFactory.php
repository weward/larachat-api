<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChatApp>
 */
class ChatAppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'hash' => sha1(1),
            'company_id' => 1,
            'name' => 'default',
            'domain' => 'http://chat.app/'
        ];
    }
}
