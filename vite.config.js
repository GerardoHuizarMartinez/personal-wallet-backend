import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',  // Aquí debe ir la ruta correcta
        'resources/js/app.js',
      ],
      refresh: true,
    }),
  ],
  build: {
    manifest: true,
    outDir: 'public/build',
    rollupOptions: {
      input: [
        'resources/css/app.css',  // También aquí debe coincidir
        'resources/js/app.js',
      ],
    },
  },
});
