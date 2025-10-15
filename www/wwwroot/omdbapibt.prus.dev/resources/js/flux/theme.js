const STORAGE_KEY = 'flux-ui-theme';
const prefersDark = () => window.matchMedia?.('(prefers-color-scheme: dark)')?.matches ?? false;

const applyTheme = (theme) => {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
};

const updateToggles = (theme) => {
    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.setAttribute('data-theme', theme);
        const labelTarget = toggle.querySelector('[data-theme-label]');
        if (labelTarget) {
            labelTarget.textContent = theme === 'dark' ? 'Light mode' : 'Dark mode';
        }
    });
};

const persistTheme = (theme) => {
    try {
        window.localStorage.setItem(STORAGE_KEY, theme);
    } catch (error) {
        console.warn('Flux theme could not be persisted', error);
    }
};

export const currentTheme = () => {
    try {
        return window.localStorage.getItem(STORAGE_KEY) ?? (prefersDark() ? 'dark' : 'light');
    } catch (error) {
        return prefersDark() ? 'dark' : 'light';
    }
};

export const toggleTheme = () => {
    const newTheme = currentTheme() === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
    persistTheme(newTheme);
    updateToggles(newTheme);
    document.dispatchEvent(new CustomEvent('flux:theme-changed', { detail: { theme: newTheme } }));
};

export const hydrateTheme = () => {
    const theme = currentTheme();
    applyTheme(theme);
    updateToggles(theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            toggleTheme();
        });
    });
};

if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hydrateTheme, { once: true });
    } else {
        hydrateTheme();
    }

    window.matchMedia?.('(prefers-color-scheme: dark)')?.addEventListener?.('change', (event) => {
        const theme = event.matches ? 'dark' : 'light';
        applyTheme(theme);
        persistTheme(theme);
        updateToggles(theme);
    });
}
