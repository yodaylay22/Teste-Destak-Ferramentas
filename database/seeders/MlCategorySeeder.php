<?php

namespace Database\Seeders;

use App\Models\MlCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MlCategorySeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path('ml_unique_categories.json');

        if (!File::exists($filePath)) {
            $this->command->error("Arquivo ml_unique_categories.json não encontrado na raiz do projeto.");
            return;
        }

        $jsonContent = File::get($filePath);

        $categories = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Erro ao decodificar o JSON: " . json_last_error_msg());
            return;
        }

        foreach ($categories as $category) {
            MlCategory::updateOrCreate(
                ['id_ml' => $category['id']],
                ['name' => $category['name']]
            );
        }

        $this->command->info('Categorias do Mercado Livre importadas com sucesso!');
    }
}