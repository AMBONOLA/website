import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                mustard: {
                    50: '#fdf9e8',
                    100: '#faf0c4',
                    200: '#f5df8a',
                    300: '#efc94d',
                    400: '#e6b422',
                    500: '#d19c12',
                    600: '#b07a0d',
                    700: '#8c590f',
                    800: '#744714',
                    900: '#633b16',
                },
            },
        },
    },

    plugins: [forms],
};
