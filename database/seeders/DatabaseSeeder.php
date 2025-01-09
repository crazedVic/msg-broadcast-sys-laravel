<?php

namespace Database\Seeders;

use App\Models\Broadcast;
use App\Models\BroadcastUserState;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 5 test users
        // Create an admin user
        User::create([
            'email' => 'ed@ed.com',
            'password' => bcrypt('passwd99'),
            'name' => 'Ed',
            'is_admin' => true,
        ]);
        
        $user = User::create([
            'email' => 'bob@bob.com',
            'password' => bcrypt('passwd99'),
            'name' => 'Bob',
            'is_admin' => false,
        ]);


        // Seed the remaining 4 users
        $users = User::factory(9)->create()->prepend($user);
        
        // Create a new user for Bob
        
        // Create 10 broadcasts with dates in the last 30 days
        Broadcast::factory(20)->create()->each(function ($broadcast) {
            $broadcast->created_at = Carbon::now()->subDays(rand(0, 30));
            $broadcast->save();
        });

        //$users = User::all();
        $broadcasts = Broadcast::all();

        // Randomly mark some broadcasts as read or deleted by some users
        foreach ($broadcasts as $broadcast) {
            // Ensure the first user doesn't read all broadcasts
            $usersToInteract = $users->skip(1)->shuffle()->take(rand(0, $users->count() - 1));

            foreach ($usersToInteract as $user) {
                if (rand(0, 2) > 0) { // Mark as read or deleted
                    BroadcastUserState::create([
                        'user_id' => $user->id,
                        'broadcast_id' => $broadcast->id,
                        'read_at' => rand(0, 1) ? Carbon::now()->subDays(rand(0, 30)) : null, // Randomly set read_at
                        'deleted_at' => rand(0, 3) === 0 ? Carbon::now()->subDays(rand(0, 30)) : null, // Randomly set deleted_at
                        'created_at' => Carbon::now()->subDays(rand(0, 30)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                    ]);
                }
            }
        }
    }
}
