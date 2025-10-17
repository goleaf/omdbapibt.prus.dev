<?php

namespace App\Livewire;

use App\Models\ListItem;
use App\Models\ListModel;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Watchlist extends Component
{
    public ?int $movieId = null;

    public bool $isSaved = false;

    public bool $isAuthenticated = false;

    public string $locale = '';

    /**
     * @var list<array{id:int,title:string,public:bool,description:?string,cover_url:?string,is_watch_later:bool,items:list<array{id:int,movie_id:int,position:int,title:string,slug:?string,poster:?string,year:?string}>}>
     */
    public array $lists = [];

    public ?int $activeListId = null;

    public int $summaryCount = 0;

    public string $newListTitle = '';

    public ?int $renamingListId = null;

    public string $renamingTitle = '';

    protected array $messages = [
        'newListTitle.required' => 'Enter a title for your list.',
        'newListTitle.max' => 'List titles must be fewer than 255 characters.',
        'renamingTitle.required' => 'Enter a title for your list.',
        'renamingTitle.max' => 'List titles must be fewer than 255 characters.',
    ];

    public function mount(?int $movieId = null): void
    {
        $this->movieId = $movieId;
        $this->isAuthenticated = Auth::check();
        $this->locale = app()->getLocale();

        if ($this->isToggleMode()) {
            $this->isSaved = $this->determineSavedState();
        } elseif ($this->isAuthenticated) {
            $this->loadLists();
        }
    }

    public function toggle(): void
    {
        if (! $this->isAuthenticated) {
            session()->flash('error', 'Sign in to save titles to your watchlist.');

            return;
        }

        if (! $this->isToggleMode()) {
            return;
        }

        $user = $this->user();

        $movie = Movie::find($this->movieId);

        if (! $movie) {
            session()->flash('error', 'We could not find this movie.');

            return;
        }

        $list = $user->ensureWatchLaterList();

        $existingItem = $list->items()->where('movie_id', $movie->getKey())->first();

        if ($existingItem instanceof ListItem) {
            $existingItem->delete();
            session()->flash('status', 'Removed from your watch later list.');
        } else {
            $list->items()->create([
                'movie_id' => $movie->getKey(),
                'position' => $list->nextPosition(),
            ]);

            session()->flash('status', 'Added to your watch later list.');
        }

        $this->isSaved = $this->determineSavedState();
        $this->dispatch('watchlist-updated');
    }

    public function removeItem(int $itemId): void
    {
        if (! $this->isAuthenticated) {
            session()->flash('error', 'Sign in to manage your watchlist.');

            return;
        }

        $user = $this->user();

        $item = ListItem::query()
            ->whereKey($itemId)
            ->whereHas('list', static function ($query) use ($user): void {
                $query->where('user_id', $user->getKey());
            })
            ->first();

        if (! $item instanceof ListItem) {
            return;
        }

        $listId = $item->list_id;
        $item->delete();

        $this->loadLists();
        $this->activeListId = $listId;
        $this->dispatch('watchlist-updated');
    }

    public function setActiveList(int $listId): void
    {
        if (! $this->isAuthenticated) {
            return;
        }

        $ownsList = collect($this->lists)->contains(static fn (array $list): bool => $list['id'] === $listId);

        if ($ownsList) {
            $this->activeListId = $listId;
        }
    }

    public function createList(): void
    {
        if (! $this->isAuthenticated) {
            session()->flash('error', 'Sign in to manage your watchlist.');

            return;
        }

        $input = ['newListTitle' => $this->newListTitle];

        $validator = Validator::make($input, [
            'newListTitle' => ['required', 'string', 'max:255'],
        ], $this->messages);

        try {
            $validator->validate();
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());

            return;
        }

        $user = $this->user();

        $list = $user->lists()->create([
            'title' => $this->newListTitle,
            'public' => false,
            'description' => null,
            'cover_url' => null,
        ]);

        $this->newListTitle = '';
        $this->loadLists();
        $this->activeListId = $list->getKey();
        $this->resetErrorBag();
    }

    public function startRenaming(int $listId): void
    {
        if (! $this->isAuthenticated) {
            return;
        }

        $list = collect($this->lists)->firstWhere('id', $listId);

        if (! $list) {
            return;
        }

        $this->renamingListId = $listId;
        $this->renamingTitle = $list['title'];
    }

    public function updateListTitle(): void
    {
        if (! $this->isAuthenticated || $this->renamingListId === null) {
            return;
        }

        $validator = Validator::make([
            'renamingTitle' => $this->renamingTitle,
        ], [
            'renamingTitle' => ['required', 'string', 'max:255'],
        ], $this->messages);

        try {
            $validator->validate();
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());

            return;
        }

        $user = $this->user();

        $user->lists()
            ->whereKey($this->renamingListId)
            ->update([
                'title' => $this->renamingTitle,
            ]);

        $this->renamingListId = null;
        $this->renamingTitle = '';
        $this->resetErrorBag();
        $this->loadLists();
    }

    public function togglePrivacy(int $listId): void
    {
        if (! $this->isAuthenticated) {
            return;
        }

        $user = $this->user();

        $list = $user->lists()->whereKey($listId)->first();

        if (! $list instanceof ListModel) {
            return;
        }

        $list->update([
            'public' => ! $list->public,
        ]);

        $this->loadLists();
        $this->activeListId = $listId;
    }

    public function deleteList(int $listId): void
    {
        if (! $this->isAuthenticated) {
            return;
        }

        $user = $this->user();

        $list = $user->lists()->whereKey($listId)->first();

        if (! $list instanceof ListModel) {
            return;
        }

        if ($list->isWatchLater()) {
            session()->flash('error', 'The default watch later list cannot be deleted.');

            return;
        }

        $list->delete();

        $this->loadLists();
        $this->activeListId = $this->lists[0]['id'] ?? null;
    }

    public function moveItemUp(int $itemId): void
    {
        $this->swapItemPosition($itemId, 'up');
    }

    public function moveItemDown(int $itemId): void
    {
        $this->swapItemPosition($itemId, 'down');
    }

    protected function swapItemPosition(int $itemId, string $direction): void
    {
        if (! $this->isAuthenticated) {
            return;
        }

        $user = $this->user();

        $item = ListItem::query()
            ->whereKey($itemId)
            ->whereHas('list', static function ($query) use ($user): void {
                $query->where('user_id', $user->getKey());
            })
            ->first();

        if (! $item instanceof ListItem) {
            return;
        }

        $query = $item->list->items()->where('id', '!=', $item->getKey());

        if ($direction === 'up') {
            $swap = $query
                ->where('position', '<', $item->position)
                ->orderByDesc('position')
                ->first();
        } else {
            $swap = $query
                ->where('position', '>', $item->position)
                ->orderBy('position')
                ->first();
        }

        if (! $swap instanceof ListItem) {
            return;
        }

        $itemPosition = $item->position;
        $item->update(['position' => $swap->position]);
        $swap->update(['position' => $itemPosition]);

        $this->loadLists();
        $this->activeListId = $item->list_id;
    }

    protected function loadLists(): void
    {
        if (! $this->isAuthenticated) {
            $this->lists = [];
            $this->activeListId = null;
            $this->summaryCount = 0;

            return;
        }

        $user = $this->user();
        $user->ensureWatchLaterList();

        $lists = $user->lists()
            ->with(['items' => function ($query): void {
                $query->orderBy('position')
                    ->with(['movie' => function ($movieQuery): void {
                        $movieQuery->select('id', 'slug', 'poster_path', 'release_date', 'title');
                    }]);
            }])
            ->orderBy('created_at')
            ->get();

        $this->lists = $lists->map(function (ListModel $list): array {
            $items = $list->items->map(function (ListItem $item): array {
                $movie = $item->movie;

                return [
                    'id' => $item->getKey(),
                    'movie_id' => $item->movie_id,
                    'position' => $item->position,
                    'title' => $movie ? $movie->localizedTitle($this->locale) : 'Untitled',
                    'slug' => $movie?->slug,
                    'poster' => $movie?->poster_path,
                    'year' => $movie && $movie->release_date ? $movie->release_date->format('Y') : null,
                ];
            })->values()->all();

            return [
                'id' => $list->getKey(),
                'title' => $list->title,
                'public' => $list->public,
                'description' => $list->description,
                'cover_url' => $list->cover_url,
                'is_watch_later' => $list->isWatchLater(),
                'items' => $items,
            ];
        })->values()->all();

        if ($this->activeListId === null && isset($this->lists[0])) {
            $this->activeListId = $this->lists[0]['id'];
        }

        $this->summaryCount = array_sum(array_map(static fn (array $list): int => count($list['items']), $this->lists));
    }

    protected function determineSavedState(): bool
    {
        if (! $this->isAuthenticated || ! $this->isToggleMode() || $this->movieId === null) {
            return false;
        }

        $user = $this->user();
        $movie = Movie::find($this->movieId);

        if (! $movie) {
            return false;
        }

        return $user->hasInWatchLater($movie);
    }

    protected function isToggleMode(): bool
    {
        return $this->movieId !== null;
    }

    protected function user(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }

    public function render()
    {
        if (! $this->isToggleMode() && $this->isAuthenticated) {
            $this->loadLists();
        }

        $activeList = collect($this->lists)->firstWhere('id', $this->activeListId);

        return view('livewire.watchlist', [
            'toggleMode' => $this->isToggleMode(),
            'locale' => $this->locale,
            'lists' => $this->lists,
            'activeList' => $activeList,
            'summaryCount' => $this->summaryCount,
        ]);
    }
}
