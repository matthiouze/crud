<?php

namespace Matt\Crud\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCrudCommand extends GeneratorCommand
{
    protected $name = 'make:crud';
    protected $description = 'Créer un CRUD pour un modèle donné avec un contrôleur, des vues et une migration';

    protected $type = 'Crud';

    /**
     * Obtenez le nom du fichier de la classe.
     *
     * @return string
     */
    protected function getStub()
    {
        // Ici, vous pouvez retourner des stubs personnalisés pour générer les fichiers de manière flexible.
        return __DIR__ . '/../stubs/crud.stub';
    }

    /**
     * Configurer les options pour la commande.
     */
    protected function getOptions()
    {
        return [
            ['controller', null, InputOption::VALUE_REQUIRED, 'Le nom du contrôleur CRUD.'],
            ['model', null, InputOption::VALUE_REQUIRED, 'Le nom du modèle pour ce CRUD.'],
        ];
    }

    /**
     * Générez les fichiers nécessaires pour le CRUD.
     */
    public function handle()
    {
        $controller = $this->option('controller');
        $model = $this->option('model');
        
        // Création du contrôleur
        $this->call('make:controller', [
            'name' => $controller,
        ]);
        
        // Générer la migration
        $this->call('make:migration', [
            'name' => 'create_' . strtolower($model) . '_table',
        ]);
        
        // Générer les vues
        $this->createViews($model);
        
        // Autres actions ici...
    }

    /**
     * Créer les vues associées au CRUD.
     *
     * @param string $model
     */
    protected function createViews($model)
    {
        $viewsPath = resource_path("views/{$model}/");
        if (!file_exists($viewsPath)) {
            mkdir($viewsPath, 0755, true);
        }

        // Créer les fichiers de vues (index, create, show)
        file_put_contents($viewsPath . 'index.blade.php', view('crud::index', ['model' => $model])->render());
        file_put_contents($viewsPath . 'create.blade.php', view('crud::create', ['model' => $model])->render());
        file_put_contents($viewsPath . 'show.blade.php', view('crud::show', ['model' => $model])->render());
    }
}

