import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import fs from 'fs';
import path from 'path';

export default defineConfig(({ command }) => {
    const isBuild = command === 'build';

    return {
        base: isBuild ? '/wp-content/themes/Portfolio/dist/' : '/',
        server: {
            port: 3000,
            cors: true,
            origin: 'http://localhost:8000',
            fs: {
                allow: ['..']
            }
        },
        // Force Vite à pré-bundler GSAP en une seule instance partagée.
        // Sans ça, Rollup peut créer plusieurs copies de GSAP dans le bundle,
        // causant un "registerPlugin is not a function" en production.
        optimizeDeps: {
            include: ['gsap', 'gsap/ScrollTrigger'],
        },
        build: {
            manifest: true,
            outDir: 'dist',
            rollupOptions: {
                input: [
                    'resources/js/app.js',
                    'resources/css/app.css',
                    'resources/css/editor-style.css'
                ],
            },
        },
        plugins: [
            tailwindcss(),
        ],
    }
});
