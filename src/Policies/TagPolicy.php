<?php

namespace Sharenjoy\NoahShop\Policies;

use Sharenjoy\NoahCms\Models\User;
use Sharenjoy\NoahCms\Models\Tag;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('view_any_tag');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Tag $tag): bool
    {
        return $user->can('view_tag');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('create_tag');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Tag $tag): bool
    {
        return $user->can('update_tag');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Tag $tag): bool
    {
        return $user->can('delete_tag');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('delete_any_tag');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Tag $tag): bool
    {
        return $user->can('force_delete_tag');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('force_delete_any_tag');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Tag $tag): bool
    {
        return $user->can('restore_tag');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('restore_any_tag');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Tag $tag): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('reorder_tag');
    }
}
