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
                primary: 'var(--primary-color, #063b96)',
                'primary-dark': 'var(--primary-color-dark, #021f59)',
                'primary-100': '#dce8fa',
                'primary-600': 'var(--primary-color, #063b96)',
                navy: '#021f59',
                cobalt: '#063b96',
                crimson: '#c4040a',
                gold: {
                    light: '#ffe27a',
                    DEFAULT: '#dca22c',
                    dark: '#8f5e0c',
                },
                emerald: {
                    verify: '#10b981',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
