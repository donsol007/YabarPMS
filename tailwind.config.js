import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

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
            colors: {
                brand: {
                    50: '#FFF5ED',
                    100: '#FFE9D6',
                    200: '#FFD0AC',
                    300: '#FDAF78',
                    400: '#F78D42',
                    500: '#F17F2B',
                    600: '#EB721E',
                    700: '#C25B16',
                    800: '#9B4912',
                    900: '#7C3C0F',
                    950: '#441F08',
                },
                indigo: {
                    50: '#FFF5ED',
                    100: '#FFE9D6',
                    200: '#FFD0AC',
                    300: '#FDAF78',
                    400: '#F78D42',
                    500: '#F17F2B',
                    600: '#EB721E',
                    700: '#C25B16',
                    800: '#9B4912',
                    900: '#7C3C0F',
                    950: '#441F08',
                },
            },
        },
    },

    plugins: [forms],
};