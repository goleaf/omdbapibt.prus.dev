// Keyboard shortcuts for search and header interactions
document.addEventListener('livewire:init', () => {
    // Cmd/Ctrl + K for search focus
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            Livewire.dispatch('focusSearch');
        }
    });

    // Escape key to close all dropdowns
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            Livewire.dispatch('closeAllDropdowns');
        }
    });

    // Listen for focus-search-input event and focus the input
    window.addEventListener('focus-search-input', () => {
        const searchInput = document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms="query"]');
        if (searchInput) {
            searchInput.focus();
        }
    });
});

