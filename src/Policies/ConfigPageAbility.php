<?php

namespace Bulbalara\CoreConfigMs\Policies;

enum ConfigPageAbility: string
{
    case VIEW_PAGE = 'viewPage';

    case SAVE = 'save';

    case VIEW_TAB = 'viewTab';

    case VIEW_GROUP = 'viewGroup';

    case VIEW_FIELD = 'viewField';

    case EDIT_TAB = 'editTab';

    case EDIT_GROUP = 'editGroup';

    case EDIT_FIELD = 'editField';
}
