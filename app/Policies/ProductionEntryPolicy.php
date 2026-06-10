<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductionEntry;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductionEntryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductionEntry');
    }

    public function view(AuthUser $authUser, ProductionEntry $productionEntry): bool
    {
        return $authUser->can('View:ProductionEntry');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductionEntry');
    }

    public function update(AuthUser $authUser, ProductionEntry $productionEntry): bool
    {
        return $authUser->can('Update:ProductionEntry');
    }

    public function delete(AuthUser $authUser, ProductionEntry $productionEntry): bool
    {
        return $authUser->can('Delete:ProductionEntry');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ProductionEntry');
    }

    public function restore(AuthUser $authUser, ProductionEntry $productionEntry): bool
    {
        return $authUser->can('Restore:ProductionEntry');
    }

    public function forceDelete(AuthUser $authUser, ProductionEntry $productionEntry): bool
    {
        return $authUser->can('ForceDelete:ProductionEntry');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductionEntry');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductionEntry');
    }

    public function replicate(AuthUser $authUser, ProductionEntry $productionEntry): bool
    {
        return $authUser->can('Replicate:ProductionEntry');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductionEntry');
    }

}