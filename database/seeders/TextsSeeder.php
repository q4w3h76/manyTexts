<?php

namespace Database\Seeders;

use App\Models\Text;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TextsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            Text::factory(4)->sequence(['user_id' => $user->id])->create();
        }
    }
}
