<?php

namespace App\Models;

use App\Enums\DocOpts;
use App\Services\DocumentNumberGenerator;

class Account extends LModel
{
    protected static function booted(): void
    {
        static::creating(function (self $account) {
            if (! $account->title) {
                if (!$account->party_id) {
                    $account->title =
                        DocumentNumberGenerator::generate(
                            $account->company,
                            DocOpts::CompanyAccount
                        );
                } else {
                    $account->title =
                        DocumentNumberGenerator::generate(
                            $account->company,
                            DocOpts::PartyAccount
                        );
                }
            }
        });
    }
    public function outboundVouchers()
    {
        return $this->hasMany(Voucher::class, 'from_account_id');
    }

    public function inboundVouchers()
    {
        return $this->hasMany(Voucher::class, 'to_account_id');
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
