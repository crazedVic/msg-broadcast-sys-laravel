<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         // Disable foreign key constraints
         DB::statement('PRAGMA foreign_keys = OFF;');

         // Get all user-created table names
         $tables = DB::select("
             SELECT name 
             FROM sqlite_master 
             WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name != 'migrations'
         ");
 
         // Drop all user-created tables
         foreach ($tables as $table) {
             $tableName = $table->name;
             DB::statement("DROP TABLE IF EXISTS \"$tableName\";");
         }
 
         // Clear the migrations table
         // DB::table('migrations')->truncate();
 
         // Re-enable foreign key constraints
         DB::statement('PRAGMA foreign_keys = ON;');
    }
};
