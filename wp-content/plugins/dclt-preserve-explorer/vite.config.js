import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  define: {
    'process.env.NODE_ENV': JSON.stringify('production'),
  },
  build: {
    lib: {
      entry: path.resolve(__dirname, './src/preserve-explorer.jsx'),
      name: 'PreserveExplorer',
      fileName: () => 'preserve-explorer.js',
      formats: ['iife'],
    },
    outDir: path.resolve(__dirname, './assets/js'),
    emptyOutDir: false,
  },
});