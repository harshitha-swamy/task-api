<?php
namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $userIds  = User::pluck('id')->toArray();
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $batchSize = 1000;
        $total     = 50000;

        $this->command->info('Seeding 50,000 tasks...');
        $bar = $this->command->getOutput()->createProgressBar($total / $batchSize);

        for ($i = 0; $i < $total / $batchSize; $i++) {
            $tasks = [];

            for ($j = 0; $j < $batchSize; $j++) {
                $tasks[] = [
                    'title'            => fake()->sentence(4),
                    'description'      => fake()->paragraph(),
                    'status'           => $statuses[array_rand($statuses)],
                    'assigned_user_id' => $userIds[array_rand($userIds)],
                    'created_by'       => $userIds[array_rand($userIds)],
                    'due_date'         => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }

            Task::insert($tasks);
            $bar->advance();
        }

        $bar->finish();
        $this->command->info("\n✅ 50,000 tasks seeded successfully!");
    }
}