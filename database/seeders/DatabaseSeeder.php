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

        // check for data flag
        $option = app()->bound('data_only') ? app('data_only') : false;

        if(!$option){
            $this->command->info('Seeding everything');

            // Create 5 test users
            // Create an admin user
            $admin = User::create([
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
        }
        else{
            $this->command->info('Seeding jsut data');
            $users = User::all();
        }
      
        // Create 10 broadcasts with dates in the last 30 days
        Broadcast::factory(20)->create()->each(function ($broadcast) {
            $broadcast->created_at = Carbon::now()->subDays(rand(0, 30));
           // $broadcast->expires_at = rand(0, 2) === 0 ? Carbon::now()->subDays(rand(0, 10)) : null;
            $broadcast->save();
        });

        $broadcasts = Broadcast::all();

        // Randomly mark some broadcasts as read or deleted by some users
        foreach ($broadcasts as $broadcast) {
            // Ensure the first user doesn't read all broadcasts
            $usersToInteract = $users->shuffle()->take(rand(0, $users->count() - 1));
            //$usersToInteract = $users->skip(1)->shuffle()->take(rand(0, $users->count()));
            foreach ($usersToInteract as $user) {
                $deleted  = rand(0, 3) === 0;
                if (rand(0, 2) > 0) { // Mark as read or deleted
                    BroadcastUserState::create([
                        'user_id' => $user->id,
                        'broadcast_id' => $broadcast->id,
                        'read_at' => $deleted || rand(0, 5) < 4 ? Carbon::now()->subDays(rand(0, 30)) : null, // Randomly set read_at
                        'deleted_at' => $deleted ? Carbon::now()->subDays(rand(0, 30)) : null, // Randomly set deleted_at
                        'created_at' => Carbon::now()->subDays(rand(0, 30)),
                        'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                    ]);
                }
            }
            // Randomly mark some broadcasts as deleted
            $broadcast->deleted_at =  rand(0, 2) === 0 ? Carbon::now()->subDays(rand(0, 30)) : null; // Randomly set deleted_at
            $broadcast->save();
        }
    }
}
