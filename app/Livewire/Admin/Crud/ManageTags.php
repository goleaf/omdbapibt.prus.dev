<?php

namespace App\Livewire\Admin\Crud;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageTags extends CrudComponent
{
    public array $merge = [
        'source_id' => '',
        'target_id' => '',
    ];

    public function mount(): void
    {
        parent::mount();

        $this->merge = [
            'source_id' => '',
            'target_id' => '',
        ];
    }

    protected function model(): string
    {
        return Tag::class;
    }

    protected function view(): string
    {
        return 'livewire.admin.crud.manage-tags';
    }

    protected function defaultForm(): array
    {
        return [
            'name' => [
                'en' => '',
                'es' => '',
                'fr' => '',
            ],
            'slug' => '',
            'type' => Tag::TYPE_SYSTEM,
        ];
    }

    protected function formRules(): array
    {
        return [
            'form.name.en' => ['required', 'string', 'max:255'],
            'form.name.es' => ['nullable', 'string', 'max:255'],
            'form.name.fr' => ['nullable', 'string', 'max:255'],
            'form.slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('tags', 'slug')->ignore($this->editingId),
            ],
            'form.type' => ['required', 'string', Rule::in(Tag::TYPES)],
        ];
    }

    protected function query(): Builder
    {
        return Tag::query()
            ->withCount('movies')
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.Str::of($this->search)->trim().'%';

                $query->where(function (Builder $inner) use ($term): void {
                    $inner
                        ->where('slug', 'like', $term)
                        ->orWhere('name_i18n->en', 'like', $term)
                        ->orWhere('name_i18n->es', 'like', $term)
                        ->orWhere('name_i18n->fr', 'like', $term);
                });
            })
            ->orderBy('slug');
    }

    protected function mutateFormData(array $data): array
    {
        $slug = Str::of(Arr::get($data, 'slug', ''))->slug('-');
        $translations = collect(Arr::get($data, 'name', []))
            ->mapWithKeys(function ($value, $locale): array {
                $value = is_string($value) ? trim($value) : '';

                if ($value === '') {
                    return [];
                }

                return [$locale => $value];
            })
            ->all();

        $primaryName = $translations['en'] ?? Arr::first($translations, fn ($value) => is_string($value) && $value !== '', '');
        $generatedSlug = $primaryName !== '' ? Str::slug($primaryName) : null;

        return [
            'slug' => $slug->isNotEmpty() ? $slug->value() : ($generatedSlug ?: Str::uuid()->toString()),
            'name_i18n' => $translations,
            'type' => in_array(Arr::get($data, 'type'), Tag::TYPES, true)
                ? Arr::get($data, 'type')
                : Tag::TYPE_SYSTEM,
        ];
    }

    protected function fillFromModel(Model $model): array
    {
        /** @var Tag $model */
        $translations = $model->name_i18n ?? [];

        return [
            'name' => [
                'en' => (string) ($translations['en'] ?? ''),
                'es' => (string) ($translations['es'] ?? ''),
                'fr' => (string) ($translations['fr'] ?? ''),
            ],
            'slug' => $model->slug ?? '',
            'type' => $model->type ?? Tag::TYPE_SYSTEM,
        ];
    }

    protected function validationAttributeLabels(): array
    {
        return [
            'form.name.en' => 'English name',
            'form.name.es' => 'Spanish name',
            'form.name.fr' => 'French name',
            'form.slug' => 'slug',
            'form.type' => 'type',
            'merge.source_id' => 'source tag',
            'merge.target_id' => 'target tag',
        ];
    }

    public function merge(): void
    {
        $this->validate(
            [
                'merge.source_id' => ['required', 'integer', Rule::exists('tags', 'id')],
                'merge.target_id' => ['required', 'integer', Rule::exists('tags', 'id')],
            ],
            [],
            $this->validationAttributeLabels()
        );

        $sourceId = (int) $this->merge['source_id'];
        $targetId = (int) $this->merge['target_id'];

        if ($sourceId === $targetId) {
            $this->addError('merge.target_id', __('validation.different', ['attribute' => __('ui.admin.panel.tags.merge.target'), 'other' => __('ui.admin.panel.tags.merge.source')]));

            return;
        }

        /** @var Tag $source */
        $source = Tag::query()->findOrFail($sourceId);
        /** @var Tag $target */
        $target = Tag::query()->findOrFail($targetId);

        $this->authorize('delete', $source);
        $this->authorize('update', $target);

        DB::transaction(function () use ($source, $target): void {
            $pivotRows = DB::table('film_tag')
                ->where('tag_id', $source->id)
                ->lockForUpdate()
                ->get();

            foreach ($pivotRows as $pivot) {
                $existing = DB::table('film_tag')
                    ->where('movie_id', $pivot->movie_id)
                    ->where('tag_id', $target->id)
                    ->when(is_null($pivot->user_id), function ($query): void {
                        $query->whereNull('user_id');
                    }, function ($query) use ($pivot): void {
                        $query->where('user_id', $pivot->user_id);
                    })
                    ->first();

                if ($existing) {
                    $newWeight = max((int) ($existing->weight ?? 0), (int) ($pivot->weight ?? 0));

                    DB::table('film_tag')
                        ->where('id', $existing->id)
                        ->update([
                            'weight' => $newWeight,
                            'updated_at' => now(),
                        ]);

                    DB::table('film_tag')->where('id', $pivot->id)->delete();

                    continue;
                }

                DB::table('film_tag')
                    ->where('id', $pivot->id)
                    ->update([
                        'tag_id' => $target->id,
                        'updated_at' => now(),
                    ]);
            }

            $source->delete();
        });

        if ($this->editingId === $sourceId) {
            $this->create();
        }

        $this->merge = [
            'source_id' => '',
            'target_id' => '',
        ];

        $this->dispatch('tags-merged');
    }
}
