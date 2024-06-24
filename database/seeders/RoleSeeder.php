<?php

namespace Database\Seeders;

use App\Models\role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $roles = [
      ['role_name' => 'user', 'description' => 'A standard user individuals role'],
      ['role_name' => 'admin', 'description' => 'An administrator role'],
      ['role_name' => 'super admin', 'description' => 'An super administrator role'],
      // Tambahkan role lain jika diperlukan
    ];

    foreach ($roles as $role) {
      role::create($role);
    }
  }
}