<?php

namespace Sharenjoy\NoahShop\Policies;

use Sharenjoy\NoahCms\Models\User;
use Sharenjoy\NoahShop\Models\Survey\Entry;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_survey::entry');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Entry $entry): bool
    {
        return $user->can('view_survey::entry');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('{{ Create }}');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Entry $entry): bool
    {
        return $user->can('{{ Update }}');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Entry $entry): bool
    {
        return $user->can('delete_survey::entry');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_survey::entry');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Entry $entry): bool
    {
        return $user->can('force_delete_survey::entry');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_survey::entry');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Entry $entry): bool
    {
        return $user->can('restore_survey::entry');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_survey::entry');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Entry $entry): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
