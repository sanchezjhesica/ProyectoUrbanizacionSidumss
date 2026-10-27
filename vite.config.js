import { readdirSync } from 'node:fs';
import { join } from 'node:path';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

function collectStylesheets(directory) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const entryPath = join(directory, entry.name);

        if (entry.isDirectory()) {
            return collectStylesheets(entryPath);
        }

        return entry.isFile() && entry.name.endsWith('.css')
            ? [entryPath.replace(/\\/g, '/')]
            : [];
    });
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...collectStylesheets('resources/css/layouts'),
                ...collectStylesheets('resources/css/views'),
            ],
            refresh: true,
        }),
    ],
});
