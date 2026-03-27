import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel(["resources/js/app.js", "resources/css/app.css"]),
    ],
});
