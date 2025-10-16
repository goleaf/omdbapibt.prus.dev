<?php

declare(strict_types=1);

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavigationLinks extends Component
{
    public string $layout;

    public string $linkClass;

    public string $containerClass;

    public function __construct(string $layout = 'horizontal', string $linkClass = 'flux-text-muted transition hover:text-emerald-300')
    {
        $this->layout = $layout;
        $this->linkClass = $linkClass;
        $this->containerClass = $this->resolveContainerClass($layout);
    }

    public function render(): View|Closure|string
    {
        return view('components.navigation-links');
    }

    private function resolveContainerClass(string $layout): string
    {
        return match ($layout) {
            'vertical' => 'flex flex-col gap-6 text-base font-medium',
            default => 'flex items-center gap-8 text-sm font-medium',
        };
    }
}
