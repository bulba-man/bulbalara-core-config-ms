<?php

namespace Bulbalara\CoreConfigMs\Handlers;

use Illuminate\Database\Eloquent\Collection;

class After implements ConfigHandlerInterface
{

    public function handle(Collection|array &$configs): void
    {
        $mailer = config('mail.transport.mailer');
        config(['mail.default' => $mailer]);
        if (config('mail.transport.'.$mailer)) {
            config(['mail.mailers.'.$mailer => array_merge(
                config('mail.mailers.'.$mailer), config('mail.transport.'.$mailer)
            )]);
        }

        config(['mail.from' => config('mail.addresses.from')]);
    }
}
