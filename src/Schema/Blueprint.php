<?php

namespace GopanKombiyil\Larango\Schema;

use ArangoDBClient\CollectionHandler;
use Closure;
use Illuminate\Database\Connection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Traits\Macroable;
use GopanKombiyil\Larango\Schema\Concerns\Columns;
use GopanKombiyil\Larango\Schema\Concerns\Indexes;
use GopanKombiyil\Larango\Schema\Concerns\Tables;

/**
 * Class Blueprint.
 *
 * The Schema blueprint works differently from the standard Illuminate version:
 * 1) ArangoDB is schemaless: we don't need to (and can't) create columns
 * 2) ArangoDB doesn't allow DB schema actions within AQL nor within a transaction.
 *
 * This means that:
 * 1) We catch column related methods silently for backwards compatibility and ease of migrating from one DB type to another
 * 2) We don't need to compile AQL for transactions within the accompanying schema grammar. (for now)
 * 3) We can just execute each command on order. We will gather them first for possible future optimisations.
 */
class Blueprint
{
    use Macroable;
    use Tables;
    use Columns;
    use Indexes;

    /**
     * The connection that is used by the blueprint.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * The grammar that is used by the blueprint.
     *
     * @var Grammar
     */
    protected $grammar;

    /**
     * The table the blueprint describes.
     *
     * @var string
     */
    protected $table;

    /**
     * The handler for table manipulation.
     */
    protected $collectionHandler;

    /**
     * The prefix of the table.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The commands that should be run for the table.
     *
     * @var Fluent[]
     */
    protected $commands = [];

    /**
     * Catching columns to be able to add fluent indexes.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Whether to make the table temporary.
     *
     * @var bool
     */
    public $temporary = false;

    /**
     * Detect if _key (and thus proxy _id) should autoincrement.
     *
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * Create a new schema blueprint.
     *
     * Blueprint constructor.
     * @param string $table
     * @param CollectionHandler $collectionHandler
     * @param Closure|null $callback
     * @param string $prefix
     */
    public function __construct($table, $collectionHandler, Closure $callback = null, $prefix = '')
    {
        $this->table = $table;

        $this->collectionHandler = $collectionHandler;

        $this->prefix = $prefix;

        if (! is_null($callback)) {
            $callback($this);
        }
    }

    /**
     * Execute the blueprint against the database.
     *
     * @param Connection $connection
     * @param Grammar $grammar
     * @return void
     */
    public function build(Connection $connection, Grammar $grammar)
    {
        $this->connection = $connection;

        if (! isset($grammar)) {
            $this->grammar = $connection->getSchemaGrammar();
        }

        foreach ($this->commands as $command) {
            if ($command->handler == 'aql') {
                $command = $this->compileAqlCommand($command);
            }

            $this->executeCommand($command);
        }
    }

    /**
     * Generate the compilation method name and call it if method exists in the Grammar object.
     *
     * @param $command
     * @return mixed
     */
    public function compileAqlCommand($command)
    {
        $compileMethod = 'compile' . ucfirst($command->name);
        if (method_exists($this->grammar, $compileMethod)) {
            return $this->grammar->$compileMethod($this->table, $command);
        }
    }

    /**
     * Generate the execution method name and call it if the method exists.
     *
     * @param $command
     */
    public function executeCommand($command)
    {
        $executeNamedMethod = 'execute' . ucfirst($command->name) . 'Command';
        $executeHandlerMethod = 'execute' . ucfirst($command->handler) . 'Command';
        if (method_exists($this, $executeNamedMethod)) {
            $this->$executeNamedMethod($command);
        } elseif (method_exists($this, $executeHandlerMethod)) {
            $this->$executeHandlerMethod($command);
        }
    }

    /**
     * Execute an AQL statement.
     *
     * @param $command
     */
    public function executeAqlCommand($command)
    {
        $this->connection->statement($command->aqb->query, $command->aqb->binds);
    }

    public function executeCollectionCommand($command)
    {
        if ($this->connection->pretending()) {
            $this->connection->logQuery('/* ' . $command->explanation . " */\n", []);

            return;
        }

        if (method_exists($this->collectionHandler, $command->method)) {
            $this->collectionHandler->{$command->method}($command->parameters);
        }
    }

    /**
     * Solely provides feedback to the developer in pretend mode.
     *
     * @param $command
     * @return null
     */
    public function executeIgnoreCommand($command)
    {
        if ($this->connection->pretending()) {
            $this->connection->logQuery('/* ' . $command->explanation . " */\n", []);

            return;
        }
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return Fluent
     */
    protected function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return Fluent
     */
    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

    /**
     * Get the commands on the blueprint.
     *
     * @return Fluent[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Silently catch unsupported schema methods. Store columns for backwards compatible fluent index creation.
     *
     * @param $method
     * @param $args
     * @return Blueprint
     */
    public function __call($method, $args)
    {
        $columnMethods = [
            'bigIncrements', 'bigInteger', 'binary', 'boolean', 'char', 'date', 'dateTime', 'dateTimeTz', 'decimal',
            'double', 'enum', 'engine', 'float', 'geometry', 'geometryCollection', 'increments', 'integer', 'ipAddress', 'json',
            'jsonb', 'lineString', 'longText', 'macAddress', 'mediumIncrements', 'mediumInteger', 'mediumText',
            'morphs', 'uuidMorphs', 'multiLineString', 'multiPoint', 'multiPolygon',
            'nullableMorphs', 'nullableUuidMorphs', 'nullableTimestamps', 'point', 'polygon', 'rememberToken',
            'set', 'smallIncrements', 'smallInteger', 'softDeletes', 'softDeletesTz', 'string',
            'text', 'time', 'timeTz', 'timestamp', 'timestampTz', 'timestamps', 'tinyIncrements', 'tinyInteger',
            'unsignedBigInteger', 'unsignedDecimal', 'unsignedInteger', 'unsignedMediumInteger', 'unsignedSmallInteger',
            'unsignedTinyInteger', 'uuid', 'year',
        ];

        if (in_array($method, $columnMethods)) {
            if (isset($args)) {
                $this->columns[] = $args;
            }
        }

        $autoIncrementMethods = ['increments', 'autoIncrement'];
        if (in_array($method, $autoIncrementMethods)) {
            $this->autoIncrement = true;
        }

        $info['method'] = $method;
        $info['explanation'] = "'$method' is ignored; Aranguent Schema Blueprint doesn't support it.";
        $this->addCommand('ignore', $info);

        return $this;
    }
}
