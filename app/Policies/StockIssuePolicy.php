<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockIssue;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockIssuePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockIssue');
    }

    public function view(AuthUser $authUser, StockIssue $stockIssue): bool
    {
        return $authUser->can('View:StockIssue');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockIssue');
    }

    public function update(AuthUser $authUser, StockIssue $stockIssue): bool
    {
        return $authUser->can('Update:StockIssue');
    }

    public function delete(AuthUser $authUser, StockIssue $stockIssue): bool
    {
        return $authUser->can('Delete:StockIssue');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockIssue');
    }

    public function restore(AuthUser $authUser, StockIssue $stockIssue): bool
    {
        return $authUser->can('Restore:StockIssue');
    }

    public function forceDelete(AuthUser $authUser, StockIssue $stockIssue): bool
    {
        return $authUser->can('ForceDelete:StockIssue');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockIssue');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockIssue');
    }

    public function replicate(AuthUser $authUser, StockIssue $stockIssue): bool
    {
        return $authUser->can('Replicate:StockIssue');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockIssue');
    }

}