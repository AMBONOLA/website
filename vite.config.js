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
    // Polling is expensive, so skip backend-only folders and check once a second.
    server: isRunningInDocker
        ? {
              host: '0.0.0.0',
              hmr: { host: 'localhost' },
              watch: {
                  usePolling: true,
                  interval: 1000,
                  binaryInterval: 3000,
                  ignored: [
                      '**/vendor/**',
                      '**/storage/**',
                      '**/bootstrap/cache/**',
                      '**/public/build/**',
                      '**/database/**',
                      '**/tests/**',
                  ],
              },
          }
        : undefined,
});
