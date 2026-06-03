<style>
    .custom-filament-footer-wrapper {
        display: flex;
        justify-content: center;
        width: 100%;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-bottom: 2rem;
        margin-top: 2rem;
    }

    .custom-filament-footer {
        width: 100%;
        border-top: 1px solid #E5E7EB;
        /* Subtle light border */
        padding-top: 1.5rem;
    }

    .custom-filament-footer-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-align: center;
        font-size: 0.875rem;
        line-height: 1.25rem;
        color: #6B7280;
        /* Neutral gray text */
    }

    /* Desktop layout split: copyright left, branding right */
    @media (min-width: 640px) {
        .custom-filament-footer-content {
            flex-direction: row;
            justify-content: space-between;
            text-align: left;
            gap: 0;
        }
    }

    .custom-filament-footer-link {
        font-weight: 500;
        color: #2563EB;
        /* Clear actionable blue */
        text-decoration: underline;
        transition: color 0.2s ease;
    }

    /* Native Dark Mode Fallbacks */
    .dark .custom-filament-footer {
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    .dark .custom-filament-footer-content {
        color: #9CA3AF;
    }

    .dark .custom-filament-footer-link {
        color: #60A5FA;
    }
</style>

<div class="custom-filament-footer-wrapper">
    <footer class="custom-filament-footer">
        <div class="custom-filament-footer-content">
            <p>
                &copy; {{ now()->format('Y') }}
                <a href="https://techsathya.in" target="_blank" rel="noopener noreferrer"
                    class="custom-filament-footer-link">
                    <img src="/images/logos/Tech_SathyA_Logo.svg" alt="TechSathya" class="h-10 inline">TechSathyA
                </a>. All rights reserved.
            </p>
            <p>
                Powered by
                <a href="https://shreshtasmg.in" target="_blank" rel="noopener noreferrer"
                    class="custom-filament-footer-link">
                    shreshtasmg.in
                </a>
            </p>
        </div>
    </footer>
</div>
