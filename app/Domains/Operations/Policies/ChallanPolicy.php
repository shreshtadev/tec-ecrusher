<?php

declare(strict_types=1);

namespace App\Domains\Operations\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Domains\Operations\Models\Challan;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChallanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Challan');
    }

    public function view(AuthUser $authUser, Challan $challan): bool
    {
        return $authUser->can('View:Challan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Challan');
    }

    public function update(AuthUser $authUser, Challan $challan): bool
    {
        return $authUser->can('Update:Challan');
    }

    public function delete(AuthUser $authUser, Challan $challan): bool
    {
        return $authUser->can('Delete:Challan');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Challan');
    }

    public function restore(AuthUser $authUser, Challan $challan): bool
    {
        return $authUser->can('Restore:Challan');
    }

    public function forceDelete(AuthUser $authUser, Challan $challan): bool
    {
        return $authUser->can('ForceDelete:Challan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Challan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Challan');
    }

    public function replicate(AuthUser $authUser, Challan $challan): bool
    {
        return $authUser->can('Replicate:Challan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Challan');
    }

}