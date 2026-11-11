import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                '2xs': ['0.75rem', { lineHeight: '1.125rem' }],
                xs: ['0.75rem', { lineHeight: '1.125rem' }],
                '13': ['0.8125rem', { lineHeight: '1.25rem' }],
                sm: ['0.875rem', { lineHeight: '1.5' }],
                base: ['1rem', { lineHeight: '1.5' }],
                lg: ['1.125rem', { lineHeight: '1.35' }],
                xl: ['1.25rem', { lineHeight: '1.3' }],
                '2xl': ['1.5rem', { lineHeight: '1.25' }],
                '3xl': ['1.875rem', { lineHeight: '1.2' }],
                '4xl': ['2.25rem', { lineHeight: '1.18' }],
            },
            colors: {
                surface: {
                    DEFAULT: '#F7F8FA',
                    subtle: '#F3F4F6',
                    elevated: '#FFFFFF',
                },
                border: {
                    DEFAULT: '#E5E7EB',
                    subtle: '#F3F4F6',
                    strong: '#D1D5DB',
                },
                'text-primary': '#111827',
                'text-secondary': '#4B5563',
                'text-muted': '#6B7280',
                brand: {
                    50: '#ECFDF5',
                    100: '#D1FAE5',
                    200: '#A7F3D0',
                    500: '#10B981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065F46',
                },
                success: '#059669',
                warning: '#D97706',
                danger: '#DC2626',
            },
            boxShadow: {
                '2xs': '0 1px 2px 0 rgb(17 24 39 / 0.04)',
                xs: '0 1px 3px 0 rgb(17 24 39 / 0.06), 0 1px 2px -1px rgb(17 24 39 / 0.04)',
                card: '0 1px 3px 0 rgb(17 24 39 / 0.05), 0 1px 2px -1px rgb(17 24 39 / 0.03)',
                'card-hover': '0 12px 24px -6px rgb(17 24 39 / 0.08), 0 4px 10px -4px rgb(17 24 39 / 0.04)',
                tray: '0 -4px 24px -4px rgb(17 24 39 / 0.14), 0 10px 30px -5px rgb(17 24 39 / 0.22)',
            },
            transitionDuration: {
                180: '180ms',
                220: '220ms',
            },
        },
    },

    plugins: [forms],
};
