<?php
use App\Models\Task;
use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            Task::create([
                'category_id' => rand(1,3),
                'name' => 'タスク' . $i,
                'start_date' => '2021-07-01',
                'end_date' => '2021-07-11',
                'incharge_user' => '担当' . $i,
                'percentage' => (rand(1,10) * 10),
            ]);
        }
    }
}
