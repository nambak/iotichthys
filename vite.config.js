import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // .env 파일 또는 쉘 변수에 VITE_DEV_PORT가 정의되어 있으면 그것을, 없으면 기본값 5174
        port: Number(process.env.VITE_DEV_PORT) || 5174,
        cors: true,
    },
});