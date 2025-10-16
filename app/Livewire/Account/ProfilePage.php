<?php

namespace App\Livewire\Account;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class ProfilePage extends Component
{
    public User $user;

    public function mount(): void
    {
        /** @var Authenticatable&User $authenticated */
        $authenticated = Auth::user();

        $this->user = $authenticated->loadMissing('profile');
    }

    public function render(): View
    {
        $profile = $this->user->profile;

        return view('livewire.account.profile-page', [
            'profile' => $profile,
            'preferences' => $this->preferences($profile),
            'favorites' => $this->favorites($profile),
            'personalInformation' => $this->personalInformation($profile),
            'socialLinks' => $this->socialLinks($profile),
        ]);
    }

    protected function preferences(?UserProfile $profile): array
    {
        if (! $profile) {
            return [];
        }

        return [
            [
                'label' => __('account.profile.sections.preferences.items.preferred_interface_language'),
                'value' => $this->displayValue($profile->preferred_language),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.preferred_audio_language'),
                'value' => $this->displayValue($profile->preferred_audio_language),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.preferred_subtitle_language'),
                'value' => $this->displayValue($profile->preferred_subtitle_language),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.content_maturity'),
                'value' => $this->displayValue($profile->content_maturity),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.autoplay_next_episode'),
                'value' => $this->booleanLabel($profile->autoplay_next_episode),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.autoplay_trailers'),
                'value' => $this->booleanLabel($profile->autoplay_trailers),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.newsletter_opt_in'),
                'value' => $this->booleanLabel(
                    $profile->newsletter_opt_in,
                    'account.profile.values.subscribed',
                    'account.profile.values.unsubscribed',
                ),
            ],
            [
                'label' => __('account.profile.sections.preferences.items.marketing_opt_in'),
                'value' => $this->booleanLabel(
                    $profile->marketing_opt_in,
                    'account.profile.values.opted_in',
                    'account.profile.values.opted_out',
                ),
            ],
        ];
    }

    protected function favorites(?UserProfile $profile): array
    {
        if (! $profile) {
            return [];
        }

        return [
            [
                'label' => __('account.profile.sections.favorites.items.favorite_genre'),
                'value' => $this->displayValue($profile->favorite_genre),
            ],
            [
                'label' => __('account.profile.sections.favorites.items.favorite_movie'),
                'value' => $this->displayValue($profile->favorite_movie),
            ],
            [
                'label' => __('account.profile.sections.favorites.items.favorite_tv_show'),
                'value' => $this->displayValue($profile->favorite_tv_show),
            ],
            [
                'label' => __('account.profile.sections.favorites.items.favorite_actor'),
                'value' => $this->displayValue($profile->favorite_actor),
            ],
            [
                'label' => __('account.profile.sections.favorites.items.favorite_director'),
                'value' => $this->displayValue($profile->favorite_director),
            ],
            [
                'label' => __('account.profile.sections.favorites.items.favorite_quote'),
                'value' => $this->displayValue($profile->favorite_quote),
            ],
        ];
    }

    protected function personalInformation(?UserProfile $profile): array
    {
        if (! $profile) {
            return [];
        }

        return [
            [
                'label' => __('account.profile.sections.personal.items.display_name'),
                'value' => $this->displayValue($profile->display_name ?? $this->user->name),
            ],
            [
                'label' => __('account.profile.sections.personal.items.tagline'),
                'value' => $this->displayValue($profile->tagline),
            ],
            [
                'label' => __('account.profile.sections.personal.items.location'),
                'value' => $this->displayValue($profile->location),
            ],
            [
                'label' => __('account.profile.sections.personal.items.timezone'),
                'value' => $this->displayValue($profile->timezone),
            ],
            [
                'label' => __('account.profile.sections.personal.items.birthday'),
                'value' => $this->formatDate($profile->birthday),
            ],
            [
                'label' => __('account.profile.sections.personal.items.bio'),
                'value' => $this->displayValue($profile->bio),
            ],
            [
                'label' => __('account.profile.sections.personal.items.discord_handle'),
                'value' => $this->displayValue($profile->discord_handle),
            ],
        ];
    }

    protected function socialLinks(?UserProfile $profile): array
    {
        if (! $profile) {
            return [];
        }

        return [
            [
                'label' => __('account.profile.sections.social.items.website_url'),
                'value' => $this->displayValue($profile->website_url),
            ],
            [
                'label' => __('account.profile.sections.social.items.twitter_url'),
                'value' => $this->displayValue($profile->twitter_url),
            ],
            [
                'label' => __('account.profile.sections.social.items.instagram_url'),
                'value' => $this->displayValue($profile->instagram_url),
            ],
            [
                'label' => __('account.profile.sections.social.items.tiktok_url'),
                'value' => $this->displayValue($profile->tiktok_url),
            ],
            [
                'label' => __('account.profile.sections.social.items.youtube_url'),
                'value' => $this->displayValue($profile->youtube_url),
            ],
            [
                'label' => __('account.profile.sections.social.items.letterboxd_url'),
                'value' => $this->displayValue($profile->letterboxd_url),
            ],
        ];
    }

    protected function displayValue(mixed $value): string
    {
        if (is_null($value) || $value === '') {
            return __('account.profile.values.not_set');
        }

        return (string) $value;
    }

    protected function booleanLabel(
        ?bool $value,
        string $trueLabel = 'account.profile.values.enabled',
        string $falseLabel = 'account.profile.values.disabled'
    ): string {
        return match ($value) {
            true => __($trueLabel),
            false => __($falseLabel),
            default => __('account.profile.values.not_set'),
        };
    }

    protected function formatDate(?Carbon $date): string
    {
        if (! $date) {
            return __('account.profile.values.not_set');
        }

        return $date->translatedFormat('F j, Y');
    }
}
