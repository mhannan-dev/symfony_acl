import { defineConfig } from 'vite';
import symfonyPlugin from 'vite-plugin-symfony';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        symfonyPlugin({
            refresh: ['templates/**/*.twig', 'src/**/*.php', 'config/**/*.yaml']
        }),
    ],
    build: {
        rollupOptions: {
            input: {
                app: "./assets/app.js"
            },
        }
    }
});
