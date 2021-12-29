const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')

module.exports = {
    mode: 'jit',
    darkMode: 'class',
    purge: {
        content: [
            './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
            './storage/framework/views/*.php',
            './resources/views/**/*.blade.php',
            './app/Services/ArticleParser/ArticlePipeline.php',
            './app/Services/ArticleParser/Pipeline/*.php',
            './app/Services/MenuBuilder.php',
        ],
        safelist: [
            'language-mermaid',
        ]
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                teal: colors.teal,
                rose: colors.rose,
            },
            maxHeight: {
                '0': '0',
                '1/4': '25%',
                '1/2': '50%',
                '3/4': '75%',
                '8/10': '80%',
                'full': '100%',
            },
            maxWidth: {
                '0': '0',
                '1/4': '25%',
                '1/2': '50%',
                '3/4': '75%',
                '8/10': '80%',
                'full': '100%',
            }
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
            display: ['dark'],
            padding: ['dark'],
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
