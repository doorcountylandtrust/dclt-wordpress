import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  css: {
    preprocessorOptions: {
      css: {
        charset: false
      }
    }
  },
  build: {
    outDir: path.resolve(__dirname, './assets/js'),
    emptyOutDir: false,
    rollupOptions: {
      input: {
        'preserve-explorer': path.resolve(__dirname, './src/preserve-explorer.jsx'),
      },
      output: {
        entryFileNames: 'preserve-explorer.js',
        inlineDynamicImports: true,
      }
    }
  },
});