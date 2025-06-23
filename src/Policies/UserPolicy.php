<?php

namespace Sharenjoy\NoahShop\Policies;

use Sharenjoy\NoahCms\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function viewAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('view_any_user');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function view(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('view_user');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function create(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('create_user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function update(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('update_user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function delete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('delete_user');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function deleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('delete_any_user');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function forceDelete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('force_delete_user');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('force_delete_any_user');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function restore(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('restore_user');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function restoreAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('restore_any_user');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function replicate(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \Sharenjoy\NoahCms\Models\User  $user
     * @return bool
     */
    public function reorder(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
