<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        Sport::updateOrCreate(
            ['slug' => 'basquetebol'],
            [
                'nome' => 'Basquetebol',
                'descricao' => 'Modalidade principal da Academia Desportiva da Mealhada, com escalões de formação e equipa sénior.',
                'ativo' => true,
            ],
        );
    }
}
