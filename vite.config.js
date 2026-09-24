import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

const isRunningInDocker = process.env.RUNNING_IN_DOCKER === 'true';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
        }),
        react(),
    ],
    // Inside docker-compose: listen on all interfaces, point the browser at localhost,
    // and poll because file events from a Windows bind mount don't reach the container.
    server: isRunningInDocker
        ? {
              host: '0.0.0.0',
              hmr: { host: 'localhost' },
              watch: { usePolling: true },
          }
        : undefined,
});
