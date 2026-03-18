<?php

namespace Bulbalara\CoreConfigMs\Handlers;

use Illuminate\Database\Eloquent\Collection;

interface ConfigHandlerInterface
{
    public function handle(Collection|array &$configs): void;
}
