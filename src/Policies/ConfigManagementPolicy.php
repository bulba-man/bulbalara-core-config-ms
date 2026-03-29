<?php

declare(strict_types=1);

namespace Bulbalara\CoreConfigMs\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Bulbalara\CoreConfigMs\ConfigModel;
use MoonShine\Laravel\Models\MoonshineUser;

class ConfigManagementPolicy
{
    use HandlesAuthorization;

    public function viewAny(MoonshineUser $user): bool
    {
        return true;
    }

    public function view(MoonshineUser $user, ConfigModel $item): bool
    {
        return true;
    }

    public function create(MoonshineUser $user): bool
    {
        return true;
    }

    public function update(MoonshineUser $user, ConfigModel $item): bool
    {
        return true;
    }

    public function delete(MoonshineUser $user, ConfigModel $item): bool
    {
        return true;
    }

    public function restore(MoonshineUser $user, ConfigModel $item): bool
    {
        return true;
    }

    public function forceDelete(MoonshineUser $user, ConfigModel $item): bool
    {
        return true;
    }

    public function massDelete(MoonshineUser $user): bool
    {
        return true;
    }
}
