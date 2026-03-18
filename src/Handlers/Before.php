<?php

namespace Bulbalara\CoreConfigMs\Handlers;

use Illuminate\Database\Eloquent\Collection;

class Before implements ConfigHandlerInterface
{
    public function handle(Collection|array &$configs): void
    {
    }
}
