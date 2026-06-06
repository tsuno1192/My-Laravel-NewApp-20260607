<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ① 工場データを作成する
        DB::table('factories')->insert([
            ['id' => 1, 'name' => '工場A', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => '工場B', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => '工場C', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ② 設備データ（設備a〜f）を作成し、それぞれの工場に紐付ける
        DB::table('equipments')->insert([
            ['id' => 1, 'factory_id' => 1, 'name' => '設備a', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'factory_id' => 1, 'name' => '設備b', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'factory_id' => 2, 'name' => '設備c', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'factory_id' => 2, 'name' => '設備d', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'factory_id' => 3, 'name' => '設備e', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'factory_id' => 3, 'name' => '設備f', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ③ 1番の工場（工場A）に所属するテストユーザーを作る
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'factory_id' => 1,            
        ]);

        // --- ここに今回作ったデータを追記 ---
        \App\Models\Book::factory(10)->create();
       
    }
}