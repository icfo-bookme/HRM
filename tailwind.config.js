import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './Modules/**/*.php',
    ],

    safelist: [
        // Attendance badges
        'bg-red-600', 'text-white', 'bg-orange-600', 'bg-green-600', 'text-orange-700',
        // Late/Early columns
        { pattern: /text-(red|orange|green|gray)-(500|600|700)/ },
        { pattern: /bg-(red|orange|green|gray)-(100|600)/ },
    ],

     theme: {
      extend: {
        fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
        colors: {
          navy: { 800: '#1e3a8a', 900: '#1e3163' },
        },
        transitionProperty: { width: 'width', 'min-width': 'min-width' },
      }
    },

    plugins: [forms],
};
