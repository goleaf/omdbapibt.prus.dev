import defaultTheme from 'tailwindcss/defaultTheme';
import plugin from 'tailwindcss/plugin';

export default {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.{js,jsx,ts,tsx}',
        './resources/css/**/*.css',
        './storage/framework/views/*.php',
        './vendor/livewire/flux/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Instrument Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fff3f3',
                    100: '#ffe0e0',
                    200: '#ffb3b6',
                    300: '#ff8087',
                    400: '#ff4d58',
                    500: '#f02d39',
                    600: '#d11422',
                    700: '#a90f1a',
                    800: '#7c0c14',
                    900: '#4d050b',
                    950: '#2b0206',
                },
                surface: {
                    50: '#f8fafc',
                    100: '#edf2f7',
                    200: '#e2e8f0',
                    300: '#cbd5f5',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#1f2937',
                    800: '#111827',
                    900: '#0b1120',
                    950: '#050812',
                },
                imdb: {
                    DEFAULT: '#f5c518',
                    400: '#fcdc6f',
                    500: '#f5c518',
                    600: '#c7950b',
                },
            },
            boxShadow: {
                spotlight: '0 20px 45px -25px rgba(239, 68, 68, 0.65)',
                neon: '0 0 0 1px rgba(255, 255, 255, 0.12), 0 10px 30px rgba(15, 23, 42, 0.45)',
            },
            backgroundImage: {
                'grid-radial':
                    'radial-gradient(circle at center, rgba(241, 245, 249, 0.3) 0, rgba(241, 245, 249, 0.05) 45%, transparent 70%)',
            },
        },
    },
    plugins: [
        plugin(({ addComponents, addUtilities, theme }) => {
            addComponents({
                '.flux-card': {
                    position: 'relative',
                    display: 'flex',
                    flexDirection: 'column',
                    gap: theme('spacing.3'),
                    color: theme('colors.surface.50'),
                    backgroundColor: 'rgba(15, 23, 42, 0.45)',
                    backdropFilter: 'blur(6px)',
                    borderRadius: theme('borderRadius.3xl'),
                    overflow: 'hidden',
                    transitionProperty: 'transform, box-shadow',
                    transitionDuration: theme('transitionDuration.300'),
                    boxShadow: theme('boxShadow.neon'),
                },
                '.flux-card:hover': {
                    transform: 'translateY(-4px)',
                    boxShadow: theme('boxShadow.spotlight'),
                },
                '.flux-card__poster': {
                    width: '100%',
                    aspectRatio: '2 / 3',
                    objectFit: 'cover',
                    transition: 'transform 0.6s ease',
                },
                '.flux-card:hover .flux-card__poster': {
                    transform: 'scale(1.08)',
                },
                '.flux-card__body': {
                    display: 'flex',
                    flexDirection: 'column',
                    gap: theme('spacing.2'),
                    padding: theme('spacing.5'),
                },
                '.flux-rating': {
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '0.375rem',
                    borderRadius: theme('borderRadius.full'),
                    paddingLeft: theme('spacing.3'),
                    paddingRight: theme('spacing.3'),
                    paddingTop: '0.375rem',
                    paddingBottom: '0.375rem',
                    fontWeight: theme('fontWeight.semibold'),
                    backgroundColor: theme('colors.imdb.DEFAULT'),
                    color: theme('colors.surface.900'),
                    boxShadow: '0 8px 20px rgba(197, 149, 11, 0.45)',
                },
                '.flux-rating[data-variant="highlight"]': {
                    backgroundColor: theme('colors.brand.500'),
                    color: theme('colors.surface.50'),
                    boxShadow: '0 12px 25px rgba(240, 45, 57, 0.35)',
                },
                '.flux-button': {
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontWeight: theme('fontWeight.medium'),
                    borderRadius: theme('borderRadius.full'),
                    paddingLeft: theme('spacing.4'),
                    paddingRight: theme('spacing.4'),
                    paddingTop: '0.625rem',
                    paddingBottom: '0.625rem',
                    transitionProperty: 'background-color, color, transform, box-shadow',
                    transitionDuration: theme('transitionDuration.200'),
                    backgroundColor: theme('colors.brand.500'),
                    color: theme('colors.surface.50'),
                    boxShadow: theme('boxShadow.spotlight'),
                },
                '.flux-button:hover': {
                    transform: 'translateY(-1px)',
                    backgroundColor: theme('colors.brand.400'),
                },
                '.flux-button[data-variant="ghost"]': {
                    backgroundColor: 'transparent',
                    color: theme('colors.surface.100'),
                    boxShadow: 'none',
                    border: '1px solid rgba(148, 163, 184, 0.4)',
                },
                '.flux-button[data-variant="ghost"]:hover': {
                    backgroundColor: 'rgba(148, 163, 184, 0.1)',
                    color: theme('colors.surface.50'),
                },
            });

            addUtilities({
                '.bg-cinematic': {
                    background:
                        'linear-gradient(120deg, rgba(10, 12, 25, 0.94), rgba(24, 10, 18, 0.8)), radial-gradient(circle at top left, rgba(240, 45, 57, 0.55), transparent 55%)',
                },
                '.glass-panel': {
                    backgroundColor: 'rgba(15, 23, 42, 0.66)',
                    backdropFilter: 'blur(18px)',
                    border: '1px solid rgba(148, 163, 184, 0.2)',
                },
            });
        }),
    ],
};
