<?php

namespace Matt\Crud\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCrudCommand extends GeneratorCommand
{
	protected $name = 'make:crud {--model}';
	protected $description = 'Créer un CRUD pour un modèle donné avec un contrôleur, des vues et une migration';

	protected $type = 'Crud';

	/**
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/controller.stub';
	}

	/**
	 * Configurer les options pour la commande.
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['model', null, InputOption::VALUE_REQUIRED, 'Crud model name is required'],
		];
	}

	/**
	 * handle crud generation
	 * @return void
	 */
	public function handle()
	{
		$model = $this->option('model');

		$this->call('make:model', ['name' => $model]);

		// Création du contrôleur
		$this->call('make:controller', ['name' => $model.'Controller']);

		// Générer la migration
		$this->call('make:migration', [
			'name' => 'create_' . strtolower($model) . 's_table',
		]);

		$this->createViews($model);

		$this->updateController($model);
	}

	/**
	 * Create views.
	 *
	 * @param string $model
	 */
	protected function createViews($model)
	{
		$viewsPath = resource_path("views/".strtolower($model)."s/");
		if (!file_exists($viewsPath)) {
			mkdir($viewsPath, 0755, true);
			mkdir($viewsPath.'/partials', 0755, true);
		}

		// Créer les fichiers de vues (index, create, show)
		file_put_contents($viewsPath . 'index.blade.php', '');
		file_put_contents($viewsPath . 'create.blade.php', '');
		file_put_contents($viewsPath . 'show.blade.php', '');
		file_put_contents($viewsPath . '/partials/form.blade.php', '');
	}

	/**
	 * @param string $controllerName
	 * @param string $model
	 */
	private function updateController(string $model)
	{
		$controllerPath = app_path("Http/Controllers/{$model}Controller.php");

		// Charger le stub du contrôleur
		$stub = file_get_contents($this->getStub());

		// Remplacer les placeholders par les valeurs dynamiques
		$controllerContent = str_replace(
			['{{ $model }}', '{{ $controllerName }}'],
			[$model, $model.'Controller'],
			$stub
		);

		// Créer ou remplacer le fichier du contrôleur
		file_put_contents($controllerPath, $controllerContent);
	}
}

