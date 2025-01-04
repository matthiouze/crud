<?php

namespace Matt\Crud;

use Illuminate\Support\ServiceProvider;
use Matt\Crud\Commands\MakeCrudCommand;

class CrudServiceProvider extends ServiceProvider{

	public function register()
	{
		$this->commands([MakeCrudCommand::class]);
	}

	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'crud-generator');
	}
}
