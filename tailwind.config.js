/** @type {import('tailwindcss').Config} */
module.exports = {
  // Files scanned for class names. The compiled stylesheet only ships the
  // utilities actually used here.
  content: [
    './index.php',
    './app/Views/**/*.php',
    './app/Views/*.php',
    './public/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        // Indigo/blue brand accent
        brand: {
          50: '#eef2ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
          950: '#1e1b4b',
        },
        // Dark UI surfaces (slate-based)
        surface: {
          50: '#f8fafc',
          100: '#e2e8f0',
          200: '#cbd5e1',
          300: '#94a3b8',
          400: '#64748b',
          500: '#475569',
          600: '#334155',
          700: '#1e293b',
          800: '#0f172a',
          900: '#0b1120',
          950: '#070b14',
        },
      },
      fontFamily: {
        sans: [
          'Inter',
          'ui-sans-serif',
          'system-ui',
          '-apple-system',
          'Segoe UI',
          'Roboto',
          'Helvetica',
          'Arial',
          'sans-serif',
        ],
      },
    },
  },
  // Preflight is disabled during the incremental migration so that loading the
  // compiled stylesheet on a page never resets/alters legacy, not-yet-migrated
  // content. It can be re-enabled once every view has been migrated.
  corePlugins: {
    preflight: false,
  },
  // 'class' strategy: only elements with .form-input/.form-select/etc. are
  // styled, so legacy form controls are left untouched until their migration.
  plugins: [require('@tailwindcss/forms')({ strategy: 'class' })],
};
