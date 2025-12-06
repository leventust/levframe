<?php

namespace Core\Console;

use Symfony\Component\Console\CommandLoader\ContainerCommandLoader as BaseLoader;
use Illuminate\Console\Command;

class ContainerCommandLoader extends BaseLoader
{
    protected $container;

    public function __construct($container, array $commandMap)
    {
        parent::__construct($container, $commandMap);
        $this->container = $container;
    }

    public function get(string $name): \Symfony\Component\Console\Command\Command
    {
        $command = parent::get($name);

        if ($command instanceof Command) {
            $command->setLaravel($this->container);
        }

        return $command;
    }
}
