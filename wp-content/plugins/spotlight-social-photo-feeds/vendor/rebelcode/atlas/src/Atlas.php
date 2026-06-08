<?php

namespace RebelCode\Atlas;

class Atlas
{
    /** @var Config */
    protected $config;

    /** @var array<string,Table> */
    protected $tables;

    /** @param Config $config */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->tables = [];
    }

    public function table(string $name, ?Schema $schema = null): Table
    {
        if (!array_key_exists($name, $this->tables) || $schema !== null) {
            $this->tables[$name] = new Table($this->config, $name, $schema);
        }

        return $this->tables[$name];
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /** @return Table[] */
    public function getTables(): array
    {
        return $this->tables;
    }

    public static function createDefault(): self
    {
        return new self(Config::createDefault());
    }
}
