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
    const fallback = theme === 'light' || theme === 'dark' ? theme : getPreferredTheme();
    const resolved = fallback === 'light' ? 'light' : 'dark';

    root.dataset.theme = resolved;
    root.classList.toggle('dark', resolved === 'dark');
    root.classList.toggle('light', resolved === 'light');
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

    if (!window._fluxThemeListeners) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: light)');
        const handleSystemChange = (event) => {
            if (getStoredTheme()) {
                return;
            }

            applyTheme(event.matches ? 'light' : 'dark');
        };

        const handleStorage = (event) => {
            if (event.key !== themeStorageKey) {
                return;
            }

            applyTheme(event.newValue ?? undefined);
        };

        mediaQuery.addEventListener('change', handleSystemChange);
        window.addEventListener('storage', handleStorage);

        window._fluxThemeListeners = {
            mediaQuery,
            handleSystemChange,
            handleStorage,
        };
    }

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

const setupMobileNavigation = () => {
    document.querySelectorAll('[data-mobile-nav]').forEach((root) => {
        const panel = root.querySelector('[data-mobile-nav-panel]');

        if (!panel) {
            return;
        }

        root._mobileNavCleanup?.();

        const openButtons = Array.from(root.querySelectorAll('[data-mobile-nav-open]'));
        const closeButtons = Array.from(root.querySelectorAll('[data-mobile-nav-close]'));
        const backdrop = root.querySelector('[data-mobile-nav-backdrop]');

        const focusableSelector = [
            'a[href]',
            'button:not([disabled])',
            'textarea:not([disabled])',
            'input:not([disabled])',
            'select:not([disabled])',
            '[tabindex]:not([tabindex="-1"])',
        ].join(',');

        let lastFocusedElement = null;

        const isDesktop = () => window.innerWidth >= 768;

        const getFocusableElements = () =>
            Array.from(panel.querySelectorAll(focusableSelector)).filter((element) => {
                if (element.hasAttribute('disabled')) {
                    return false;
                }

                if (element.getAttribute('aria-hidden') === 'true') {
                    return false;
                }

                const style = window.getComputedStyle(element);
                return style.display !== 'none' && style.visibility !== 'hidden';
            });

        const syncBodyScroll = (locked) => {
            if (locked) {
                document.body.classList.add('overflow-hidden');
            } else {
                document.body.classList.remove('overflow-hidden');
            }
        };

        const setOpen = (open, { silent = false } = {}) => {
            const desktop = isDesktop();
            const resolved = desktop ? true : Boolean(open);

            root.dataset.open = resolved ? 'true' : 'false';
            panel.setAttribute('aria-hidden', resolved ? 'false' : 'true');
            panel.setAttribute('aria-modal', !desktop && resolved ? 'true' : 'false');
            openButtons.forEach((button) => button.setAttribute('aria-expanded', resolved ? 'true' : 'false'));

            if (desktop) {
                panel.classList.remove('translate-x-full', 'opacity-0', 'pointer-events-none');
                panel.classList.add('translate-x-0', 'opacity-100', 'pointer-events-auto');
                backdrop?.classList.add('opacity-0', 'pointer-events-none');
                backdrop?.classList.remove('opacity-100', 'pointer-events-auto');
                syncBodyScroll(false);
                return;
            }

            if (resolved) {
                panel.classList.remove('translate-x-full', 'opacity-0', 'pointer-events-none');
                panel.classList.add('translate-x-0', 'opacity-100', 'pointer-events-auto');
                backdrop?.classList.add('opacity-100', 'pointer-events-auto');
                backdrop?.classList.remove('opacity-0', 'pointer-events-none');

                if (!silent) {
                    lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;

                    const focusable = getFocusableElements();
                    (focusable[0] ?? panel).focus({ preventScroll: true });
                }

                syncBodyScroll(true);
            } else {
                panel.classList.add('translate-x-full', 'opacity-0', 'pointer-events-none');
                panel.classList.remove('translate-x-0', 'opacity-100', 'pointer-events-auto');
                backdrop?.classList.remove('opacity-100', 'pointer-events-auto');
                backdrop?.classList.add('opacity-0', 'pointer-events-none');

                if (!silent && lastFocusedElement) {
                    lastFocusedElement.focus({ preventScroll: true });
                }

                lastFocusedElement = null;
                syncBodyScroll(false);
            }
        };

        const handleOpen = (event) => {
            event.preventDefault();
            setOpen(true);
        };

        const handleClose = (event) => {
            event.preventDefault();
            setOpen(false);
        };

        const handleBackdrop = () => setOpen(false);

        const handleKeydown = (event) => {
            if (event.key === 'Escape') {
                setOpen(false);
                return;
            }

            if (event.key !== 'Tab' || root.dataset.open !== 'true' || isDesktop()) {
                return;
            }

            const focusable = getFocusableElements();

            if (focusable.length === 0) {
                event.preventDefault();
                panel.focus({ preventScroll: true });
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            const active = document.activeElement;

            if (event.shiftKey && active === first) {
                event.preventDefault();
                last.focus({ preventScroll: true });
            } else if (!event.shiftKey && active === last) {
                event.preventDefault();
                first.focus({ preventScroll: true });
            }
        };

        const handleResize = () => {
            if (isDesktop()) {
                setOpen(true, { silent: true });
            } else {
                const shouldStayOpen = root.dataset.open === 'true';
                setOpen(shouldStayOpen, { silent: true });
                syncBodyScroll(shouldStayOpen);
            }
        };

        openButtons.forEach((button) => button.addEventListener('click', handleOpen));
        closeButtons.forEach((button) => button.addEventListener('click', handleClose));
        panel.addEventListener('keydown', handleKeydown);
        backdrop?.addEventListener('click', handleBackdrop);
        window.addEventListener('resize', handleResize);

        root._mobileNavCleanup = () => {
            openButtons.forEach((button) => button.removeEventListener('click', handleOpen));
            closeButtons.forEach((button) => button.removeEventListener('click', handleClose));
            panel.removeEventListener('keydown', handleKeydown);
            backdrop?.removeEventListener('click', handleBackdrop);
            window.removeEventListener('resize', handleResize);
            syncBodyScroll(false);
        };

        const initialOpen = root.dataset.open === 'true';
        setOpen(initialOpen, { silent: true });
        handleResize();
    });
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
    setupMobileNavigation();
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
        setupMobileNavigation();
    });

    setupThemeToggle();
    setupMobileNavigation();
});
