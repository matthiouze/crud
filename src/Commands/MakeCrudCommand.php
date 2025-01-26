<?php

namespace Matt\Crud\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeCrudCommand extends GeneratorCommand
{
    protected $name         = 'make:crud {--model}';
    protected $description  = 'Créer un CRUD pour un modèle donné avec un contrôleur, des vues et une migration';
    protected $type         = 'Crud';

	/**
	 * @return array
	 */
	protected function getStub(): array
	{
		return [
            'controller' => __DIR__ . '/stubs/controller.stub',
            'test'       => __DIR__ . '/stubs/test.stub',
            'route'      => __DIR__ . '/stubs/web.stub',
            'factory'    => __DIR__ . '/stubs/factory.stub',
        ];
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
	public function handle(): void
	{
		$model = $this->option('model');

		$this->call('make:model', ['name' => $model]);
		$this->call('make:controller', ['name' => $model.'Controller']);
        $this->call('make:test', ['name' => $model.'Test']);
        $this->call('make:factory', ['name' => $model.'Factory']);
		$this->call('make:migration', [
			'name' => 'create_' . strtolower($model) . 's_table',
		]);

		$this->createViews($model);

		$this->updateController($model);
		$this->updateTest($model);
		$this->updateRoute($model);
		$this->updateFactory($model);
	}

	/**
	 * Create views.
	 * @param string $model
     * @return void
	 */
	protected function createViews($model): void
	{
		$path = resource_path("views/".strtolower($model)."s/");
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
			mkdir($path.'/partials', 0755, true);
		}

		// Créer les fichiers de vues (index, create, show)
		file_put_contents($path . 'index.blade.php', '');
		file_put_contents($path . 'create.blade.php', '');
		file_put_contents($path . 'edit.blade.php', '');
		file_put_contents($path . 'show.blade.php', '');
		file_put_contents($path . '/partials/form.blade.php', '');
	}

	/**
	 * @param string $model
     * @return void
	 */
	private function updateController(string $model): void
	{
        $path = app_path("Http/Controllers/{$model}Controller.php");
		$stub = file_get_contents($this->getStub());

        $content = str_replace(
			['{{ $model }}', '{{ $controllerName }}'],
			[$model, $model.'Controller'],
			$stub['controller']
		);

		file_put_contents($path, $content);
	}

    /**
     * @param string $model
     * @return void
     */
    private function updateTest(string $model): void
    {
        $path = base_path("tests/Feature/{$model}Test.php");
        $stub = file_get_contents($this->getStub());

        $content = str_replace(
            ['{{ $model }}', '{{ $name }}'],
            [$model, strtolower($model)],
            $stub['test']
        );

        file_put_contents($path, $content);
    }

    /**
     * @param string $model
     * @return void
     */
    private function updateRoute(string $model): void
    {
        $path = base_path("routes/web.php");
        $stub = file_get_contents($this->getStub());

        $content = str_replace(
            ['{{ $model }}', '{{ $name }}'],
            [$model, strtolower($model)],
            $stub['route']
        );

        file_put_contents($path, $content);
    }

    /**
     * @param string $model
     * @return void
     */
    private function updateFactory(string $model): void
    {
        $path = base_path("database/factories/{$model}Factory.php");
        $stub = file_get_contents($this->getStub());

        $content = str_replace(
            ['{{ $model }}'],
            [$model],
            $stub['factory']
        );

        file_put_contents($path, $content);
    }
}

