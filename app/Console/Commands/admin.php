<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:admin {email} {password} {name}')]
#[Description('Command description')]
class admin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $email = $this->argument('email');
            $password = $this->argument('password');
            $name = $this->argument('name');

            $admin = \App\Models\User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
                'is_banned' => false,
                'is_approved' => true,
            ]);
            
            $admin->assignRole('admin');
            $this->info("Admin user created successfully with email: {$email}");
        }catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }
}
