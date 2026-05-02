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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'app-bg':         '#FFFFFF',
                'app-bg-soft':    '#F7F8FA',
                'app-text':       '#0F172A',
                'app-text-muted': '#64748B',
                'app-accent':     '#2563EB',
                'app-accent-hov': '#1D4ED8',
                'app-positive':   '#16A34A',
                'app-negative':   '#DC2626',
                'app-warning':    '#F59E0B',
                'app-border':     '#E5E7EB',
            },
        },
    },

    plugins: [forms],
};
