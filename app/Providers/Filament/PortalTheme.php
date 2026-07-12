<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Support\ServiceProvider;

class PortalTheme extends ServiceProvider
{
    public function boot(): void
    {
        Panel::configureUsing(function (Panel $panel) {
            if (in_array($panel->getId(), ['yonetici', 'ogretmen', 'veli'])) {
                $panel
                    ->colors([
                        'primary' => [
                            50 => '#f0f9ff',
                            100 => '#e0f2fe',
                            200 => '#bae6fd',
                            300 => '#7dd3fc',
                            400 => '#38bdf8',
                            500 => '#0ea5e9',
                            600 => '#0284c7',
                            700 => '#0369a1',
                            800 => '#075985',
                            900 => '#0c4a6e',
                            950 => '#082f49',
                        ],
                        'secondary' => [
                            50 => '#fdf4ff',
                            100 => '#fae8ff',
                            200 => '#f5d0fe',
                            300 => '#f0abfc',
                            400 => '#e879f9',
                            500 => '#d946ef',
                            600 => '#c026d3',
                            700 => '#a21caf',
                            800 => '#86198f',
                            900 => '#701a75',
                            950 => '#4a044e',
                        ],
                        'success' => [
                            50 => '#f0fdf4',
                            100 => '#dcfce7',
                            200 => '#bbf7d0',
                            300 => '#86efac',
                            400 => '#4ade80',
                            500 => '#22c55e',
                            600 => '#16a34a',
                            700 => '#15803d',
                            800 => '#166534',
                            900 => '#14532d',
                            950 => '#052e16',
                        ],
                        'warning' => [
                            50 => '#fffbeb',
                            100 => '#fef3c7',
                            200 => '#fde68a',
                            300 => '#fcd34d',
                            400 => '#fbbf24',
                            500 => '#f59e0b',
                            600 => '#d97706',
                            700 => '#b45309',
                            800 => '#92400e',
                            900 => '#78350f',
                            950 => '#451a03',
                        ],
                        'danger' => [
                            50 => '#fef2f2',
                            100 => '#fee2e2',
                            200 => '#fecaca',
                            300 => '#fca5a5',
                            400 => '#f87171',
                            500 => '#ef4444',
                            600 => '#dc2626',
                            700 => '#b91c1c',
                            800 => '#991b1b',
                            900 => '#7f1d1d',
                            950 => '#450a0a',
                        ],
                        'info' => [
                            50 => '#f0f4ff',
                            100 => '#e0ecff',
                            200 => '#c7ddff',
                            300 => '#a4c7ff',
                            400 => '#818dff',
                            500 => '#5e8df7',
                            600 => '#4c72e5',
                            700 => '#3f5cd3',
                            800 => '#364db8',
                            900 => '#304399',
                            950 => '#1e2b6b',
                        ],
                        'gray' => [
                            50 => '#f8fafc',
                            100 => '#f1f5f9',
                            200 => '#e2e8f0',
                            300 => '#cbd5e1',
                            400 => '#94a3b8',
                            500 => '#64748b',
                            600 => '#475569',
                            700 => '#334155',
                            800 => '#1e293b',
                            900 => '#0f172a',
                            950 => '#020617',
                        ],
                    ])
                    ->renderHook(
                        'panels::head.end',
                        fn (): string => '<style>
                            :root {
                                --background: 248 250 252;
                                --foreground: 15 23 42;
                                --card: 255 255 255;
                                --card-foreground: 15 23 42;
                                --popover: 255 255 255;
                                --popover-foreground: 15 23 42;
                                --primary: 14 165 233;
                                --primary-foreground: 255 255 255;
                                --secondary: 217 70 239;
                                --secondary-foreground: 255 255 255;
                                --muted: 241 245 249;
                                --muted-foreground: 100 116 139;
                                --accent: 240 249 255;
                                --accent-foreground: 15 23 42;
                                --destructive: 239 68 68;
                                --destructive-foreground: 255 255 255;
                                --border: 226 232 240;
                                --input: 226 232 240;
                                --ring: 14 165 233;
                                --radius: 0.75rem;
                            }
                            
                            .dark {
                                --background: 15 23 42;
                                --foreground: 248 250 252;
                                --card: 30 41 59;
                                --card-foreground: 248 250 252;
                                --popover: 30 41 59;
                                --popover-foreground: 248 250 252;
                                --primary: 14 165 233;
                                --primary-foreground: 255 255 255;
                                --secondary: 217 70 239;
                                --secondary-foreground: 255 255 255;
                                --muted: 30 41 59;
                                --muted-foreground: 148 163 184;
                                --accent: 30 41 59;
                                --accent-foreground: 248 250 252;
                                --destructive: 239 68 68;
                                --destructive-foreground: 255 255 255;
                                --border: 51 65 85;
                                --input: 51 65 85;
                                --ring: 14 165 233;
                            }
                            
                            /* Portal specific soft pastel design */
                            .filament-panel {
                                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
                            }
                            
                            .filament-card {
                                background: rgba(255, 255, 255, 0.95);
                                backdrop-filter: blur(8px);
                                border: 1px solid rgba(226, 232, 240, 0.8);
                                box-shadow: 0 8px 32px rgba(14, 165, 233, 0.1);
                                border-radius: 12px;
                            }
                            
                            .filament-button {
                                border-radius: 999px;
                                font-weight: 600;
                                transition: all 0.3s ease;
                                position: relative;
                                overflow: hidden;
                            }
                            
                            .filament-button::before {
                                content: "";
                                position: absolute;
                                top: 0;
                                left: -100%;
                                width: 100%;
                                height: 100%;
                                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                                transition: left 0.5s;
                            }
                            
                            .filament-button:hover::before {
                                left: 100%;
                            }
                            
                            .filament-button:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 6px 20px rgba(14, 165, 233, 0.3);
                            }
                            
                            /* Role-specific color schemes */
                            .role-yonetici { --primary: 34 197 94; }
                            .role-ogretmen { --primary: 239 68 68; }
                            .role-veli { --primary: 245 158 11; }
                            .role-ogrenci { --primary: 168 85 247; }
                            
                            /* Enhanced form styling */
                            .filament-input, .filament-select, .filament-textarea {
                                border-radius: 8px;
                                border: 2px solid var(--border);
                                transition: all 0.2s ease;
                            }
                            
                            .filament-input:focus, .filament-select:focus, .filament-textarea:focus {
                                border-color: var(--primary);
                                box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
                            }
                            
                            /* Custom scrollbar */
                            ::-webkit-scrollbar {
                                width: 10px;
                                height: 10px;
                            }
                            
                            ::-webkit-scrollbar-track {
                                background: #f1f5f9;
                                border-radius: 5px;
                            }
                            
                            ::-webkit-scrollbar-thumb {
                                background: linear-gradient(135deg, #cbd5e1, #94a3b8);
                                border-radius: 5px;
                            }
                            
                            ::-webkit-scrollbar-thumb:hover {
                                background: linear-gradient(135deg, #94a3b8, #64748b);
                            }
                            
                            /* Animation enhancements */
                            @keyframes fadeInUp {
                                from {
                                    opacity: 0;
                                    transform: translateY(20px);
                                }
                                to {
                                    opacity: 1;
                                    transform: translateY(0);
                                }
                            }
                            
                            .filament-card {
                                animation: fadeInUp 0.5s ease-out;
                            }
                            
                            /* Dashboard widget styling */
                            .filament-widget {
                                background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(240, 249, 255, 0.9));
                                border: 1px solid rgba(226, 232, 240, 0.6);
                                border-radius: 16px;
                                box-shadow: 0 4px 16px rgba(14, 165, 233, 0.08);
                            }
                        </style>'
                    );
            }
        });
    }
}
