<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Lotixam')
            ->colors([
                'primary' => Color::hex('#b1e90e'),
                'gray' => Color::Zinc,
                'danger' => Color::Rose,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString(<<<'CSS'
                <style>
                    :root {
                        --sidebar-bg: #2b2b2b;
                        --sidebar-bg-hover: #383838;
                        --sidebar-text: #e0e0e0;
                        --sidebar-text-muted: #9a9a9a;
                        --sidebar-border: rgba(255,255,255,.08);
                        --lotixam-green: #b1e90e;
                    }

                    /* Sidebar dark anthracite */
                    .fi-sidebar {
                        background-color: var(--sidebar-bg) !important;
                    }

                    /* Sidebar header (logo) */
                    .fi-body-has-topbar .fi-sidebar-header {
                        background-color: var(--sidebar-bg) !important;
                        ring: none !important;
                        box-shadow: none !important;
                        border-bottom: 1px solid var(--sidebar-border);
                    }

                    /* Brand name color */
                    .fi-sidebar .fi-logo {
                        color: #fff !important;
                    }

                    /* Group labels */
                    .fi-sidebar-group-label {
                        color: var(--sidebar-text-muted) !important;
                        text-transform: uppercase;
                        font-size: .7rem !important;
                        letter-spacing: .06em;
                    }

                    /* Group icons */
                    .fi-sidebar-group-btn > .fi-icon,
                    .fi-sidebar-group-dropdown-trigger-btn > .fi-icon {
                        color: var(--sidebar-text-muted) !important;
                    }

                    /* Nav item labels */
                    .fi-sidebar-item-label {
                        color: var(--sidebar-text) !important;
                    }

                    /* Nav item icons */
                    .fi-sidebar-item-btn > .fi-icon {
                        color: var(--sidebar-text-muted) !important;
                    }

                    /* Nav item hover */
                    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:hover,
                    .fi-sidebar-item.fi-sidebar-item-has-url > .fi-sidebar-item-btn:focus-visible {
                        background-color: var(--sidebar-bg-hover) !important;
                    }

                    /* Active nav item */
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
                        background-color: rgba(177, 233, 14, .12) !important;
                    }
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-icon {
                        color: var(--lotixam-green) !important;
                    }
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label {
                        color: var(--lotixam-green) !important;
                        font-weight: 600;
                    }
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-grouped-border > .fi-sidebar-item-grouped-border-part {
                        background-color: var(--lotixam-green) !important;
                    }

                    /* Grouped border dots */
                    .fi-sidebar-item-grouped-border-part {
                        background-color: var(--sidebar-text-muted) !important;
                    }
                    .fi-sidebar-item-grouped-border-part-not-first,
                    .fi-sidebar-item-grouped-border-part-not-last {
                        background-color: rgba(255,255,255,.15) !important;
                    }

                    /* Collapse / toggle buttons */
                    .fi-sidebar-group-collapse-btn {
                        color: var(--sidebar-text-muted) !important;
                    }

                    /* Sidebar close overlay */
                    .fi-sidebar-close-overlay {
                        background-color: rgba(0,0,0,.6) !important;
                    }

                    /* Database notifications btn in sidebar */
                    .fi-sidebar-database-notifications-btn > .fi-icon {
                        color: var(--sidebar-text-muted) !important;
                    }
                    .fi-sidebar-database-notifications-btn > .fi-sidebar-database-notifications-btn-label {
                        color: var(--sidebar-text) !important;
                    }
                    .fi-sidebar-database-notifications-btn:hover {
                        background-color: var(--sidebar-bg-hover) !important;
                    }

                    /* Scrollbar sidebar */
                    .fi-sidebar-nav::-webkit-scrollbar { width: 4px; }
                    .fi-sidebar-nav::-webkit-scrollbar-track { background: transparent; }
                    .fi-sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 2px; }

                    /* Topbar subtle branding: thin green line */
                    .fi-topbar::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        height: 2px;
                        background: linear-gradient(90deg, var(--lotixam-green), transparent 70%);
                        opacity: .5;
                    }
                    .fi-topbar { position: relative; }
                </style>
                CSS)
            )
            ->navigationGroups([
                'Opérations',
                'Commercial',
                'Finances',
                'Administration',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
