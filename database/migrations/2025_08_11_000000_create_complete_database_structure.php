<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        // This migration creates the complete database structure
        // based on the provided SQL dump file
        
        // Note: Most tables should already exist from previous migrations
        // This migration ensures all constraints and data are properly set
        
        // Ensure foreign key constraints are properly set for passations table
        if (Schema::hasTable('passations')) {
            Schema::table('passations', function (Blueprint $table) {
                // Check if foreign keys don't exist before adding them
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'passations' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                $existingConstraints = array_column($foreignKeys, 'CONSTRAINT_NAME');
                
                if (!in_array('passations_user_id_foreign', $existingConstraints)) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
                
                if (!in_array('passations_salle_id_foreign', $existingConstraints)) {
                    $table->foreign('salle_id')->references('id')->on('salles')->onDelete('cascade');
                }
            });
        }
        
        // Ensure foreign key constraints are properly set for passation_edit_logs table
        if (Schema::hasTable('passation_edit_logs')) {
            Schema::table('passation_edit_logs', function (Blueprint $table) {
                // Check if foreign keys don't exist before adding them
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'passation_edit_logs' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                
                $existingConstraints = array_column($foreignKeys, 'CONSTRAINT_NAME');
                
                if (!in_array('passation_edit_logs_passation_id_foreign', $existingConstraints)) {
                    $table->foreign('passation_id')->references('id')->on('passations')->onDelete('cascade');
                }
                
                if (!in_array('passation_edit_logs_user_id_foreign', $existingConstraints)) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
        
        // Insert sample data if tables are empty (for development/testing)
        $this->insertSampleData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove foreign key constraints
        if (Schema::hasTable('passation_edit_logs')) {
            Schema::table('passation_edit_logs', function (Blueprint $table) {
                $table->dropForeign(['passation_id']);
                $table->dropForeign(['user_id']);
            });
        }
        
        if (Schema::hasTable('passations')) {
            Schema::table('passations', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['salle_id']);
            });
        }
    }
    
    /**
     * Insert sample data for development/testing
     */
    private function insertSampleData()
    {
        // Insert sample users if users table is empty
        if (DB::table('users')->count() == 0) {
            DB::table('users')->insert([
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'email_verified_at' => null,
                    'password' => '$2y$10$h/Ke7gUFjKJb.n3isOZpXOP6Tf4lm3D8IC0SnWv4zbrRZ5b2tAYhS', // password
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'role' => 'admin'
                ],
                [
                    'id' => 2,
                    'name' => 'Med1',
                    'email' => 'med1@example.com',
                    'email_verified_at' => null,
                    'password' => '$2y$10$s52mLgmEetO31I/Opjb8fuaI82YzD6orvJ6S.J2rFCKGNyMD5TBBe', // password
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'role' => 'medecin'
                ],
                [
                    'id' => 3,
                    'name' => 'mhmd',
                    'email' => 'NoOne@gmail.com',
                    'email_verified_at' => null,
                    'password' => '$2y$10$SI/x5VvZDexm799t3Jr39uFu6ZQQt2tA4dqBQdHyec5NVdS8Gnz2C', // password
                    'remember_token' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'role' => 'medecin'
                ]
            ]);
        }
        
        // Insert sample salles if salles table is empty
        if (DB::table('salles')->count() == 0) {
            DB::table('salles')->insert([
                [
                    'id' => 1,
                    'nom' => 'salle 1.1',
                    'nombre_lits' => 6,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 2,
                    'nom' => 'salle 1.2',
                    'nombre_lits' => 10,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id' => 3,
                    'nom' => 'salle 1.6',
                    'nombre_lits' => 60,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
        
        // Insert sample passations if passations table is empty
        if (DB::table('passations')->count() == 0) {
            DB::table('passations')->insert([
                [
                    'id' => 20,
                    'nom_patient' => 'Lbien',
                    'description' => "test\r\ntest\r\ntest\r\ntest\r\ntest",
                    'date_passation' => '2025-08-02 13:18:00',
                    'user_id' => 3,
                    'created_at' => '2025-08-11 11:17:00',
                    'updated_at' => '2025-08-11 11:17:00',
                    'prenom' => 'Bilal',
                    'cin' => 'CD123',
                    'ip' => '1',
                    'salle_id' => 1
                ],
                [
                    'id' => 21,
                    'nom_patient' => 'Lbien',
                    'description' => "Bilal Bilal\r\n Bilal\r\n Bilal",
                    'date_passation' => '2025-08-11 13:22:00',
                    'user_id' => 3,
                    'created_at' => '2025-08-11 11:17:23',
                    'updated_at' => '2025-08-11 11:17:23',
                    'prenom' => 'Bilal',
                    'cin' => 'CD123',
                    'ip' => '1',
                    'salle_id' => 1
                ],
                [
                    'id' => 22,
                    'nom_patient' => 'Lamkhantar',
                    'description' => "mhmd\r\nmhmd\r\nmhmd\r\n\r\nmhmd",
                    'date_passation' => '2025-08-14 13:21:00',
                    'user_id' => 3,
                    'created_at' => '2025-08-11 11:19:21',
                    'updated_at' => '2025-08-11 11:19:21',
                    'prenom' => 'Mohammed',
                    'cin' => 'CD123sd',
                    'ip' => '2',
                    'salle_id' => 2
                ],
                [
                    'id' => 23,
                    'nom_patient' => 'Idrissi',
                    'description' => "fadlo\r\n\r\nfadlo\r\nfadlo\r\nfadlo",
                    'date_passation' => '2025-07-31 14:20:00',
                    'user_id' => 3,
                    'created_at' => '2025-08-11 11:20:34',
                    'updated_at' => '2025-08-11 11:20:34',
                    'prenom' => 'fadlo',
                    'cin' => 'ssss',
                    'ip' => '3',
                    'salle_id' => 2
                ],
                [
                    'id' => 24,
                    'nom_patient' => 'Lbien',
                    'description' => 'sssssssssssssssssssssssssssssss',
                    'date_passation' => '2025-07-30 16:27:00',
                    'user_id' => 2,
                    'created_at' => '2025-08-11 11:27:13',
                    'updated_at' => '2025-08-11 11:27:13',
                    'prenom' => 'Bilal',
                    'cin' => 'CD123',
                    'ip' => '1',
                    'salle_id' => 1
                ]
            ]);
        }
    }
};

