const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

module.exports = {
    darkMode: 'class',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                teal: colors.teal,
                rose: colors.rose,
            }
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
            display: ['dark'],
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
