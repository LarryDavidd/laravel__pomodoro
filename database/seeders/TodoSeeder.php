<?php
// database/seeders/TodoSeeder.php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if ($user) {
            $priorities = ['No priority', 'Low priority', 'Medium priority', 'High priority'];
            
            for ($i = 1; $i <= 5; $i++) {
                Todo::create([
                    'user_id' => $user->id,
                    'title' => "Пример задачи {$i}",
                    'pomodoro_value' => rand(1, 6),
                    'time_create' => now()->subDays(rand(0, 30)),
                    'is_complete' => rand(0, 1),
                    'priority' => $priorities[rand(0, 3)],
                ]);
            }
        }
    }
}