<?php

namespace Bulbalara\CoreConfigMs\Policies;

use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Models\MoonshineUser;

class ConfigPolicy
{
    /**
     * Determine whether the user can view config page.
     */
    public function viewPage(MoonshineUser $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can save configs.
     */
    public function save(MoonshineUser $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view tab.
     */
    public function viewTab(MoonshineUser $user, string $tabName): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view group in tab.
     */
    public function viewGroup(MoonshineUser $user, string $tabName, string $groupName): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view group field in tab.
     */
    public function viewField(MoonshineUser $user, string $tabName, string $groupName, FieldContract $field): bool
    {
        return true;
    }

    /**
     * Determine whether the user can edit tab.
     */
    public function editTab(MoonshineUser $user, string $tabName): bool
    {
        return true;
    }

    /**
     * Determine whether the user can edit group in tab.
     */
    public function editGroup(MoonshineUser $user, string $tabName, string $groupName): bool
    {
        return true;
    }

    /**
     * Determine whether the user can edit group field in tab.
     */
    public function editField(MoonshineUser $user, string $tabName, string $groupName, FieldContract $field): bool
    {
        return true;
    }
}
