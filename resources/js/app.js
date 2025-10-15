import './bootstrap';

const themeStorageKey = 'omdb-theme';

const getStoredTheme = () => {
    try {
        return localStorage.getItem(themeStorageKey);
    } catch (error) {
        console.warn('Unable to access theme preference storage.', error);
        return null;
    }
};

const storeTheme = (theme) => {
    try {
        localStorage.setItem(themeStorageKey, theme);
    } catch (error) {
        console.warn('Unable to persist theme preference.', error);
    }
};

const getPreferredTheme = () => {
    const stored = getStoredTheme();

    if (stored === 'light' || stored === 'dark') {
        return stored;
    }

    if (window.matchMedia('(prefers-color-scheme: light)').matches) {
        return 'light';
    }

    return 'dark';
};

const applyTheme = (theme) => {
    const root = document.documentElement;
    const resolved = theme === 'light' ? 'light' : 'dark';

    root.dataset.theme = resolved;
    root.classList.toggle('dark', resolved === 'dark');
    document.body.classList.toggle('light', resolved === 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.dataset.theme = resolved;

        const darkIcon = button.querySelector('[data-theme-icon="dark"]');
        const lightIcon = button.querySelector('[data-theme-icon="light"]');

        if (darkIcon && lightIcon) {
            if (resolved === 'dark') {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            } else {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            }
        }

        const text = button.querySelector('[data-theme-label]');

        if (text) {
            text.textContent = resolved === 'dark' ? text.dataset.labelDark : text.dataset.labelLight;
        }
    });
};

const setupThemeToggle = () => {
    const currentTheme = getPreferredTheme();
    applyTheme(currentTheme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        if (button._themeHandler) {
            return;
        }

        const handleClick = () => {
            const resolved = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            applyTheme(resolved);
            storeTheme(resolved);
        };

        button.addEventListener('click', handleClick);
        button._themeHandler = handleClick;
    });
};

const setupSidebar = (root) => {
    const sidebar = root.querySelector('[data-catalog-sidebar]');

    if (! sidebar) {
        return;
    }

    const previousState = sidebar.dataset.open === 'true';

    root._sidebarCleanup?.();

    const toggles = Array.from(root.querySelectorAll('[data-catalog-sidebar-toggle]'));
    const closeButtons = Array.from(root.querySelectorAll('[data-catalog-sidebar-close]'));
    const overlay = root.querySelector('[data-catalog-sidebar-overlay]');

    const setOpen = (open, options = {}) => {
        const isDesktop = window.innerWidth >= 1024;
        const resolved = isDesktop ? true : Boolean(open);

        sidebar.dataset.open = resolved ? 'true' : 'false';
        sidebar.setAttribute('aria-hidden', resolved ? 'false' : 'true');

        toggles.forEach((button) => button.setAttribute('aria-expanded', resolved ? 'true' : 'false'));

        if (isDesktop) {
            sidebar.classList.remove('hidden');
        } else {
            sidebar.classList.toggle('hidden', !resolved);
        }

        if (overlay) {
            overlay.classList.toggle('hidden', !(resolved && !isDesktop));
        }

        if (!isDesktop && resolved && !options.silent) {
            sidebar.focus({ preventScroll: true });
        }

        if (!isDesktop && !resolved && !options.silent) {
            const firstToggle = toggles[0];
            if (firstToggle) {
                firstToggle.focus({ preventScroll: true });
            }
        }
    };

    const handleToggle = (event) => {
        event.preventDefault();
        const isOpen = sidebar.dataset.open === 'true';
        setOpen(!isOpen);
    };

    const handleClose = (event) => {
        event.preventDefault();
        setOpen(false);
    };

    const handleOverlayClick = () => setOpen(false);

    const handleKeydown = (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    };

    const handleResize = () => {
        if (window.innerWidth >= 1024) {
            setOpen(true, { silent: true });
        } else {
            setOpen(sidebar.dataset.open === 'true', { silent: true });
        }
    };

    toggles.forEach((button) => button.addEventListener('click', handleToggle));
    closeButtons.forEach((button) => button.addEventListener('click', handleClose));
    overlay?.addEventListener('click', handleOverlayClick);
    sidebar.addEventListener('keydown', handleKeydown);
    window.addEventListener('resize', handleResize);

    root._sidebarCleanup = () => {
        toggles.forEach((button) => button.removeEventListener('click', handleToggle));
        closeButtons.forEach((button) => button.removeEventListener('click', handleClose));
        overlay?.removeEventListener('click', handleOverlayClick);
        sidebar.removeEventListener('keydown', handleKeydown);
        window.removeEventListener('resize', handleResize);
    };

    setOpen(previousState, { silent: true });
    handleResize();
};

const setupInfiniteScroll = (root) => {
    const sentinel = root.querySelector('[data-infinite-scroll-target]');
    const componentId = root.getAttribute('data-component-id');

    if (! sentinel || ! componentId) {
        return;
    }

    root._infiniteObserver?.disconnect();

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const canLoadMore = sentinel.getAttribute('data-can-load-more') === 'true';

            if (!canLoadMore) {
                return;
            }

            if (typeof window.Livewire === 'undefined') {
                return;
            }

            const component = window.Livewire.find(componentId);

            if (component) {
                component.call('loadMore');
            }
        });
    }, { rootMargin: '0px 0px 320px 0px', threshold: 0 });

    observer.observe(sentinel);
    root._infiniteObserver = observer;
};

const bootCatalogModules = () => {
    document.querySelectorAll('[data-infinite-scroll="true"]').forEach((root) => {
        setupSidebar(root);
        if (typeof window.Livewire !== 'undefined') {
            setupInfiniteScroll(root);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setupThemeToggle();
    bootCatalogModules();
});

document.addEventListener('livewire:init', () => {
    bootCatalogModules();

    window.Livewire.hook('message.processed', (_message, component) => {
        const root = component.el.closest('[data-infinite-scroll="true"]');

        if (!root) {
            return;
        }

        setupSidebar(root);
        setupInfiniteScroll(root);
    });

    setupThemeToggle();
});
