<?php

namespace Sharenjoy\NoahShop\Policies;

use Sharenjoy\NoahCms\Models\User;
use Sharenjoy\NoahShop\Models\MinSpendPromo;
use Illuminate\Auth\Access\HandlesAuthorization;

class MinSpendPromoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('view_any_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, MinSpendPromo $minSpendPromo): bool
    {
        return $user->can('view_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('create_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, MinSpendPromo $minSpendPromo): bool
    {
        return $user->can('update_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, MinSpendPromo $minSpendPromo): bool
    {
        return $user->can('delete_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('delete_any_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, MinSpendPromo $minSpendPromo): bool
    {
        return $user->can('force_delete_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('force_delete_any_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, MinSpendPromo $minSpendPromo): bool
    {
        return $user->can('restore_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('restore_any_shop::min::spend::promo');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, MinSpendPromo $minSpendPromo): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
