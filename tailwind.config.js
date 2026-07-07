import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import { createRequire } from 'module';

const require = createRequire(import.meta.url);

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                eco: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                },
                emerald: {
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                },
                usat: {
                    blue: '#1E3A8A',      // Azul Marino USAT
                    dark: '#0F172A',      // Pizarra Oscuro
                    gold: '#F59E0B',      // Amarillo/Oro USAT
                    light: '#F8FAFC',     // Gris Claro Fondo
                }
            },
        },
    },

    plugins: [forms],
};
