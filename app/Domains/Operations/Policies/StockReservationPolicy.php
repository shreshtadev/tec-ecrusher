<?php

declare(strict_types=1);

namespace App\Domains\Operations\Policies;

use App\Domains\Operations\Models\StockReservation;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StockReservationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockReservation');
    }

    public function view(AuthUser $authUser, StockReservation $stockReservation): bool
    {
        return $authUser->can('View:StockReservation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockReservation');
    }

    public function update(AuthUser $authUser, StockReservation $stockReservation): bool
    {
        return $authUser->can('Update:StockReservation');
    }

    public function delete(AuthUser $authUser, StockReservation $stockReservation): bool
    {
        return $authUser->can('Delete:StockReservation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockReservation');
    }

    public function restore(AuthUser $authUser, StockReservation $stockReservation): bool
    {
        return $authUser->can('Restore:StockReservation');
    }

    public function forceDelete(AuthUser $authUser, StockReservation $stockReservation): bool
    {
        return $authUser->can('ForceDelete:StockReservation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockReservation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockReservation');
    }

    public function replicate(AuthUser $authUser, StockReservation $stockReservation): bool
    {
        return $authUser->can('Replicate:StockReservation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockReservation');
    }
}
