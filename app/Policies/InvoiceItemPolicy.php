<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InvoiceItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoiceItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InvoiceItem');
    }

    public function view(AuthUser $authUser, InvoiceItem $invoiceItem): bool
    {
        return $authUser->can('View:InvoiceItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InvoiceItem');
    }

    public function update(AuthUser $authUser, InvoiceItem $invoiceItem): bool
    {
        return $authUser->can('Update:InvoiceItem');
    }

    public function delete(AuthUser $authUser, InvoiceItem $invoiceItem): bool
    {
        return $authUser->can('Delete:InvoiceItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InvoiceItem');
    }

    public function restore(AuthUser $authUser, InvoiceItem $invoiceItem): bool
    {
        return $authUser->can('Restore:InvoiceItem');
    }

    public function forceDelete(AuthUser $authUser, InvoiceItem $invoiceItem): bool
    {
        return $authUser->can('ForceDelete:InvoiceItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InvoiceItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InvoiceItem');
    }

    public function replicate(AuthUser $authUser, InvoiceItem $invoiceItem): bool
    {
        return $authUser->can('Replicate:InvoiceItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InvoiceItem');
    }

}