<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statusOptions = ['todo', 'done'];

        return [
            'user_id'      => \App\Models\User::factory(),
            'parent_id'    => null,  // Assuming most tasks won't be sub-tasks. Can be overridden as needed.
            'status'       => $this->faker->randomElement($statusOptions),
            'priority'     => $this->faker->numberBetween(1, 5),
            'title'        => $this->faker->sentence,
            'description'  => $this->faker->paragraph,
            'createdAt'    => $this->faker->dateTimeThisYear,
            'completedAt'  => $this->faker->optional(0.5)->dateTimeThisYear, // 50% chance of being null.
        ];
    }
}
