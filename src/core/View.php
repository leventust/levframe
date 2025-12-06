<?php

namespace Core;

use League\Plates\Engine;

class View
{
    protected Engine $engine;

    public function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    public function render($name, array $data = [])
    {
        return $this->engine->render($name, $data);
    }

    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
