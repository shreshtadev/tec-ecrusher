<?php

declare(strict_types=1);

namespace App\Domains\Master\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Domains\Master\Models\StockLevel;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockLevelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockLevel');
    }

    public function view(AuthUser $authUser, StockLevel $stockLevel): bool
    {
        return $authUser->can('View:StockLevel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockLevel');
    }

    public function update(AuthUser $authUser, StockLevel $stockLevel): bool
    {
        return $authUser->can('Update:StockLevel');
    }

    public function delete(AuthUser $authUser, StockLevel $stockLevel): bool
    {
        return $authUser->can('Delete:StockLevel');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockLevel');
    }

    public function restore(AuthUser $authUser, StockLevel $stockLevel): bool
    {
        return $authUser->can('Restore:StockLevel');
    }

    public function forceDelete(AuthUser $authUser, StockLevel $stockLevel): bool
    {
        return $authUser->can('ForceDelete:StockLevel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockLevel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockLevel');
    }

    public function replicate(AuthUser $authUser, StockLevel $stockLevel): bool
    {
        return $authUser->can('Replicate:StockLevel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockLevel');
    }

}