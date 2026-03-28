<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:admin {name} {email} {password}')]
#[Description('Command description')]
class admin extends Command
{
   
    public function handle()
    {
        try{
  
            $name = $this->argument('name');
            $password = $this->argument('password');
            $email = $this->argument('email');
    
        
                $user = \App\Models\User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($password),
                    'role' => 'admin',
    
                ]);
                $user->assignRole('admin');
                $this->info("Admin user {$name} created successfully.");
    
            return Command::SUCCESS;
        }catch(\Exception $e){
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
