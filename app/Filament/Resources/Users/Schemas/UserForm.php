<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true), // Prevents duplicate email crashes

                DateTimePicker::make('email_verified_at')
                    ->native(false)
                    ->maxDate(now()), // Prevents picking future validation dates

                TextInput::make('password')
                    ->password()
                    ->revealable()
                    // 1. Keeps original password intact if this field is left blank
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    // 2. Hashes the new password automatically before saving
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    // 3. Only forces input when creating a user, not when editing
                    ->required(fn (string $operation): bool => $operation === 'create')
                    // 4. Informs the user visually that a password already exists
                    ->placeholder(
                        fn (string $operation): string => $operation === 'edit' ? '••••••••' : 'Enter a password'
                    )
                    // 5. Explicitly explains the behavior underneath the input box
                    ->helperText(
                        fn (string $operation): ?string => $operation === 'edit' ? 'Leave this blank to keep your current password.' : null
                    )
                    ->rule(Password::default()),
            ]);
    }
}
