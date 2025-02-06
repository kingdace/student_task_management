import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path"; // Add path import

export default defineConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
            "@css": path.resolve(__dirname, "resources/css"), // Add alias for css
            "@helper": path.resolve(__dirname, "resources/js/src/helper"),
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.ts"],
            refresh: true,
        }),
        vue(),
    ],
});
