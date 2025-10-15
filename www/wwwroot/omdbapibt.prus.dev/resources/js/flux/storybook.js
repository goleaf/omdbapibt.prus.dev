const activateStory = (story, tab) => {
    story.querySelectorAll('[data-story-panel]').forEach((panel) => {
        panel.toggleAttribute('hidden', panel.getAttribute('data-story-panel') !== tab);
    });

    story.querySelectorAll('[data-story-tab]').forEach((button) => {
        const isActive = button.getAttribute('data-story-tab') === tab;
        button.setAttribute('data-active', String(isActive));
    });
};

const registerStory = (story) => {
    if (story.__fluxStoryInitialised) {
        return;
    }

    const defaultTab = story.getAttribute('data-default-tab') ?? 'preview';
    activateStory(story, defaultTab);

    story.querySelectorAll('[data-story-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            activateStory(story, button.getAttribute('data-story-tab'));
        });
    });

    story.__fluxStoryInitialised = true;
};

export const hydrateStories = () => {
    document.querySelectorAll('[data-story]').forEach(registerStory);
};

if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hydrateStories, { once: true });
    } else {
        hydrateStories();
    }

    document.addEventListener('livewire:navigated', hydrateStories);
}
