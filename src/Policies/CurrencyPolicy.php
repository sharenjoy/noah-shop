<?php

namespace Sharenjoy\NoahShop\Policies;

use Sharenjoy\NoahCms\Models\User;
use Sharenjoy\NoahShop\Models\Currency;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('view_any_currency');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Currency $currency): bool
    {
        return $user->can('view_currency');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ Create }}');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Currency $currency): bool
    {
        return $user->can('update_currency');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Currency $currency): bool
    {
        return $user->can('{{ Delete }}');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ DeleteAny }}');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Currency $currency): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Currency $currency): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(\Sharenjoy\NoahCms\Models\User | \Sharenjoy\NoahShop\Models\User $user, Currency $currency): bool
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
