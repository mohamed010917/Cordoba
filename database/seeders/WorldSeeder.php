<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Nnjeim\World\Actions\SeedAction;

class WorldSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M');

        $this->call([
            SeedAction::class,
        ]);
    }
}
