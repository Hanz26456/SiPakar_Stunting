import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['"Fira Code"', 'SF Mono', 'Monaco', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                metis: {
                    primary: '#2563eb',
                    'primary-hover': '#1d4ed8',
                    'primary-light': '#3b82f6',
                    'primary-subtle': 'rgba(37, 99, 235, 0.1)',
                    secondary: '#52525b',
                    success: '#16a34a',
                    warning: '#d97706',
                    danger: '#dc2626',
                    info: '#0891b2',
                    app: 'var(--surface-app)',
                    panel: 'var(--surface-panel)',
                    sunken: 'var(--surface-sunken)',
                    hover: 'var(--surface-hover)',
                    border: 'var(--border-color)',
                    border2: 'var(--border-color-translucent)',
                    ink: 'var(--ink-primary)',
                    'ink-secondary': 'var(--ink-secondary)',
                    'ink-muted': 'var(--ink-muted)',
                },
                // Alias orbit tokens to Metis tokens so existing utility classes adopt Metis seamlessly
                orbit: {
                    bg: 'var(--surface-app)',
                    surface: 'var(--surface-panel)',
                    surface2: 'var(--surface-sunken)',
                    surface3: 'var(--surface-hover)',
                    border: 'var(--border-color)',
                    border2: 'var(--border-color-translucent)',
                    primary: '#2563eb',
                    'primary-light': '#3b82f6',
                    'primary-glow': 'rgba(37, 99, 235, 0.2)',
                    accent: '#0891b2',
                    'accent-light': '#06b6d4',
                    'accent-glow': 'rgba(8, 145, 178, 0.2)',
                    success: '#16a34a',
                    warning: '#d97706',
                    danger: '#dc2626',
                    info: '#2563eb',
                },
            },
            boxShadow: {
                'metis': '0 1px 3px rgba(9, 9, 11, 0.05), 0 1px 2px rgba(9, 9, 11, 0.03)',
                'metis-lg': '0 8px 24px rgba(9, 9, 11, 0.08), 0 2px 6px rgba(9, 9, 11, 0.04)',
            },
        },
    },

    plugins: [forms],
};
