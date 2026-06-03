@php
    $company = \App\Domains\Master\Models\Company::first();
@endphp

<div class="flex items-center gap-2">
    @if ($company?->logo)
        <div class="h-12 w-12 [&>svg]:h-12 [&>svg]:w-12">
            {!! Storage::disk('local')->get($company->logo) !!}
        </div>
    @endif

    <span class="font-bold text-lg">
        {{ config('app.name') }}
    </span>
</div>
