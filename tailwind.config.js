/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                /* MOSALA+ National Color Palette */
                'congo': {
                    'blue': '#007FFF',      // Primary Congo Blue
                    'gold': '#F7D000',      // Attention/Warning
                    'red': '#CE1021',       // Danger/Logout (National Red)
                },
                
                /* Permanent Dark Mode Foundation */
                'dark': {
                    'page': '#0A0F1C',      // Deep Midnight Blue - Primary background
                    'surface': '#111827',   // Rich Navy Gray - Sidebar/Cards
                    'border': 'rgba(255,255,255,0.05)',  // Subtle borders
                    'border-light': 'rgba(255,255,255,0.10)',
                },
                
                /* Legacy support */
                'congo-blue': '#007FFF',
                'congo-gold': '#F7D000',
                'congo-red': '#CE1021',
                'page-dark': '#0A0F1C',
                'surface-dark': '#111827',
                'border-white-10': 'rgba(255,255,255,0.10)',
                'text-secondary': '#94A3B8',
                'glass-5': 'rgba(255,255,255,0.05)',
                'glass-10': 'rgba(255,255,255,0.10)'
            },

            backgroundColor: {
                'glass': {
                    'xs': 'rgba(255, 255, 255, 0.01)',
                    'sm': 'rgba(255, 255, 255, 0.02)',
                    'md': 'rgba(255, 255, 255, 0.03)',
                    'lg': 'rgba(255, 255, 255, 0.05)',
                    'xl': 'rgba(255, 255, 255, 0.08)',
                },
                'glass-light': 'rgba(255, 255, 255, 0.08)',
                'glass-dark': 'rgba(255, 255, 255, 0.05)',
            },

            backdropBlur: {
                'glass': '6px',
                'md-glass': '10px',
                'lg-glass': '16px',
            },

            boxShadow: {
                'glow-blue': '0 0 20px rgba(0, 127, 255, 0.2)',
                'glow-blue-lg': '0 0 40px rgba(0, 127, 255, 0.3)',
            },

            transitionDuration: {
                'theme': '500ms'
            },

            borderColor: {
                'glass': 'rgba(255, 255, 255, 0.05)',
            }
        }
    },
    plugins: [],
}
