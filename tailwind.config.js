export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Services/ArticleParser/ArticlePipeline.php',
        './app/Services/ArticleParser/Pipeline/*.php',
        './app/Services/MenuBuilder.php',
    ],

    theme: {
        extend: {
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

    plugins: ['@tailwindcss/forms'],
};
