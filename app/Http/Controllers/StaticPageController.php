<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class StaticPageController extends Controller
{
    public function terms(string $locale): View
    {
        return view('pages.terms', [
            'supportEmail' => $this->resolveSupportEmail(),
            'sections' => $this->resolveSections('terms'),
        ]);
    }

    public function privacy(string $locale): View
    {
        return view('pages.privacy', [
            'supportEmail' => $this->resolveSupportEmail(),
            'sections' => $this->resolveSections('privacy', includeItems: true),
        ]);
    }

    public function about(string $locale): View
    {
        $supportEmail = $this->resolveSupportEmail();

        return view('pages.about', [
            'supportEmail' => $supportEmail,
            'sections' => $this->resolveSections('about', includeItems: true, supportEmail: $supportEmail),
        ]);
    }

    public function support(string $locale): View
    {
        $supportEmail = $this->resolveSupportEmail();

        return view('pages.support', [
            'supportEmail' => $supportEmail,
            'sections' => $this->resolveSections('support', includeItems: true, supportEmail: $supportEmail),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolveSections(string $page, bool $includeItems = false, ?string $supportEmail = null): array
    {
        $rawSections = trans("ui.pages.{$page}.sections");

        if (! is_iterable($rawSections)) {
            return [];
        }

        $sections = [];

        foreach ($rawSections as $section) {
            $paragraphs = array_values(array_filter(
                Arr::wrap(data_get($section, 'paragraphs', [])),
                static fn ($value) => filled($value),
            ));

            $items = $includeItems
                ? array_values(array_filter(
                    Arr::wrap(data_get($section, 'items', [])),
                    static fn ($value) => filled($value),
                ))
                : [];

            $cta = $this->resolveCta($section, $supportEmail);

            $sections[] = [
                'title' => data_get($section, 'title'),
                'paragraphs' => $paragraphs,
                'items' => $items,
                'cta' => $cta,
            ];
        }

        return $sections;
    }

    /**
     * @return array{href: string, label: string}|null
     */
    private function resolveCta(mixed $section, ?string $supportEmail): ?array
    {
        $cta = data_get($section, 'cta');

        if (! is_array($cta)) {
            return null;
        }

        $email = $supportEmail ?? $this->resolveSupportEmail();

        return [
            'href' => $cta['href'] ?? ('mailto:'.$email),
            'label' => $cta['label'] ?? trans('ui.pages.support.default_cta'),
        ];
    }

    private function resolveSupportEmail(): string
    {
        return config('support.contact_email', config('mail.from.address', 'support@omdbstream.test'));
    }
}
