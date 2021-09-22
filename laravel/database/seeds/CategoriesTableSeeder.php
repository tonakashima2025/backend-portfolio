<?php
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 3; $i++) {
            Category::create([
                'name' => 'カテゴリー' . $i,
                'collapsed' => 1,
            ]);
        }
    }
}
