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
            colors: {
                primary: 'var(--primary-color, #051c45)',
                'primary-dark': 'var(--primary-color-dark, #03112b)',
                'primary-100': '#dce5f5',
                'primary-600': 'var(--primary-color, #051c45)',
                navy: '#051c45',
                crimson: '#c30102',
                emerald: {
                    verify: '#10b981',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
