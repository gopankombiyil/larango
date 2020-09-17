<?php

namespace Gopankombiyil\Larango;

use Illuminate\Support\ServiceProvider;
use Gopankombiyil\Larango\Eloquent\Model;
use Gopankombiyil\Larango\Schema\Grammar as SchemaGrammar;

class LarangoServiceProvider extends ServiceProvider
{
    /**
     * Components to register on the provider.
     *
     * @var array
     */
    protected $components = [
        'Migration',
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Add database driver.
        $this->app->resolving(
            'db',
            function ($db) {
                $db->extend(
                    'arangodb',
                    function ($config, $name) {
                        $config['name'] = $name;
                        $connection = new Connection($config);
                        $connection->setSchemaGrammar(new SchemaGrammar());

                        return $connection;
                    }
                );
            }
        );

        $this->app->resolving(
            function ($app) {
                if (class_exists('Illuminate\Foundation\AliasLoader')) {
                    $loader = \Illuminate\Foundation\AliasLoader::getInstance();
                    $loader->alias('Eloquent', 'Gopankombiyil\Larango\Eloquent\Model');
                    $loader->alias('Schema', 'Gopankombiyil\Larango\Facade\Schema');
                }
            }
        );
        $this->app->register('Gopankombiyil\Larango\Providers\CommandServiceProvider');
    }
}
