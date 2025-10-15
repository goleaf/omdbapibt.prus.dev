import './bootstrap';

const initFluxSidebarToggles = () => {
    const toggles = document.querySelectorAll('[data-flux-sidebar-toggle]');
    const getSidebar = (id) => document.getElementById(id);
    const getOverlay = (id) => document.querySelector(`[data-flux-sidebar-overlay][data-target="${id}"]`);

    const toggleSidebar = (id, force) => {
        const sidebar = getSidebar(id);
        if (!sidebar) {
            return;
        }

        const overlay = getOverlay(id);
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        const shouldOpen = typeof force === 'boolean' ? force : !isOpen;

        sidebar.classList.toggle('-translate-x-full', !shouldOpen);
        sidebar.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');

        if (overlay) {
            overlay.classList.toggle('opacity-0', !shouldOpen);
            overlay.classList.toggle('pointer-events-none', !shouldOpen);
        }
    };

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const target = toggle.dataset.target;
            if (!target) {
                return;
            }

            toggleSidebar(target);
        });
    });

    document.querySelectorAll('[data-flux-sidebar-overlay]').forEach((overlay) => {
        overlay.addEventListener('click', () => {
            const target = overlay.dataset.target;
            if (!target) {
                return;
            }

            toggleSidebar(target, false);
        });
    });

    window.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        document.querySelectorAll('[data-flux-sidebar]').forEach((sidebar) => {
            if (!sidebar.id) {
                return;
            }

            toggleSidebar(sidebar.id, false);
        });
    });
};

const initInfiniteScroll = () => {
    const observers = new Map();

    const setupSentinel = (element) => {
        if (observers.has(element)) {
            return;
        }

        const componentRoot = element.closest('[wire\\:id]');
        if (!componentRoot) {
            return;
        }

        const componentId = componentRoot.getAttribute('wire:id');
        if (!componentId || typeof window.Livewire === 'undefined') {
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const component = window.Livewire.find(componentId);
                component?.call('loadMore');
            });
        }, { rootMargin: '200px 0px' });

        observer.observe(element);
        observers.set(element, observer);
    };

    const scanSentinels = () => {
        document.querySelectorAll('[data-infinite-scroll-sentinel]').forEach((element) => {
            setupSentinel(element);
        });

        observers.forEach((observer, element) => {
            if (!document.body.contains(element)) {
                observer.disconnect();
                observers.delete(element);
            }
        });
    };

    if (typeof window.Livewire === 'undefined') {
        document.addEventListener('livewire:load', () => {
            scanSentinels();
            window.Livewire.hook('message.processed', scanSentinels);
        }, { once: true });
        return;
    }

    scanSentinels();
    window.Livewire.hook('message.processed', scanSentinels);
};

document.addEventListener('DOMContentLoaded', () => {
    initFluxSidebarToggles();
    initInfiniteScroll();
});

