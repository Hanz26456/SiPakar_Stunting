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
            },
            colors: {
                orbit: {
                    bg: 'var(--bg)',
                    surface: 'var(--surface)',
                    surface2: 'var(--surface2)',
                    surface3: 'var(--surface3)',
                    border: 'var(--border)',
                    border2: 'var(--border2)',
                    primary: '#7C3AED',
                    'primary-light': '#8B5CF6',
                    'primary-glow': 'rgba(124, 58, 237, 0.25)',
                    accent: '#06B6D4',
                    'accent-light': '#22D3EE',
                    'accent-glow': 'rgba(6, 182, 212, 0.2)',
                    success: '#10B981',
                    warning: '#F59E0B',
                    danger: '#EF4444',
                    info: '#3B82F6',
                },
            },
        },
    },

    plugins: [forms],
};
