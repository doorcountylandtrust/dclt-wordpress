/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './**/*.php',
    './assets/**/*.js',
    './components/*.css',
  ],
  safelist: [
    // number colors
    'text-brand-700','text-brand-500','text-neutral-900','text-neutral-700','text-white',
    // label colors
    'text-neutral-700','text-neutral-600','text-neutral-900','text-white'
  ],
  theme: {
    extend: {
      colors: {
        // Keep existing (don't break current buttons)
        brand: {
          DEFAULT: '#006847'
        },
        //  new structured system
        primary: {
          50: '#f0f9f4',
          500: '#006847',  // Same as brand.DEFAULT
          700: '#004d33',
          900: '#002d1f'
        },
        // Semantic colors
        success: '#10b981',
        warning: '#f59e0b', 
        danger: '#ef4444',
        info: '#3b82f6',
        // Neutral grays 
        neutral: {
          50: '#f9fafb',
          100: '#f3f4f6',
          500: '#6b7280',
          700: '#374151',
          900: '#111827'
        },
        // Preserve-specific tokens (moved inside colors)
        preserve: {
          accessible: '#3b82f6',
          activity: '#10b981',
          difficulty: '#f59e0b'
        }
      }
    }
  },
  plugins: []
}