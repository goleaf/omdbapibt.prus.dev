import './bootstrap';

const storageKey = 'omdb-theme-preference';
const root = document.documentElement;
const prefersDark = typeof window.matchMedia === 'function'
    ? window.matchMedia('(prefers-color-scheme: dark)')
    : null;

const safeGet = () => {
    try {
        return window.localStorage.getItem(storageKey);
    } catch (error) {
        return null;
    }
};

const safeSet = (value) => {
    try {
        window.localStorage.setItem(storageKey, value);
    } catch (error) {
        // Ignore persistence failures (private browsing, etc.).
    }
};

const syncToggles = (mode) => {
    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.setAttribute('aria-pressed', mode === 'dark' ? 'true' : 'false');
        toggle.dataset.themeCurrent = mode;
    });
};

const applyTheme = (mode, persist = true) => {
    const nextMode = mode === 'dark' ? 'dark' : 'light';

    if (nextMode === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }

    root.dataset.theme = nextMode;
    root.style.colorScheme = nextMode;

    if (persist) {
        safeSet(nextMode);
    }

    syncToggles(nextMode);
};

const resolveTheme = () => {
    const stored = safeGet();

    if (stored === 'dark' || stored === 'light') {
        return stored;
    }

    return prefersDark?.matches ? 'dark' : 'light';
};

applyTheme(resolveTheme(), false);

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const current = root.classList.contains('dark') ? 'dark' : 'light';
            const nextMode = current === 'dark' ? 'light' : 'dark';

            applyTheme(nextMode);
        });
    });
});

const handlePreferenceChange = (event) => {
    if (safeGet() === null) {
        applyTheme(event.matches ? 'dark' : 'light', false);
    }
};

if (prefersDark) {
    if (typeof prefersDark.addEventListener === 'function') {
        prefersDark.addEventListener('change', handlePreferenceChange);
    } else if (typeof prefersDark.addListener === 'function') {
        prefersDark.addListener(handlePreferenceChange);
    }
}
