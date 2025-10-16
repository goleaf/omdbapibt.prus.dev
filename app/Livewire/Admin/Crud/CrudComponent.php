<?php

namespace App\Livewire\Admin\Crud;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

abstract class CrudComponent extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public int $perPage = 10;

    public ?int $editingId = null;

    public array $form = [];

    public function mount(): void
    {
        $this->authorize('viewAny', $this->model());

        $this->form = $this->defaultForm();
    }

    #[Computed]
    public function records(): LengthAwarePaginator
    {
        return $this->query()->paginate($this->perPage);
    }

    public function render(): View
    {
        return view($this->view());
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->form = $this->defaultForm();
    }

    public function edit(int $id): void
    {
        $model = $this->findModel($id);

        $this->authorize('update', $model);

        $this->editingId = $id;
        $this->form = $this->fillFromModel($model);
    }

    public function delete(int $id): void
    {
        $model = $this->findModel($id);

        $this->authorize('delete', $model);

        $model->delete();

        if ($this->editingId === $id) {
            $this->create();
        }

        $this->dispatch('record-deleted');
    }

    public function save(): void
    {
        $this->validate($this->formRules(), [], $this->validationAttributes());

        $payload = $this->mutateFormData($this->form);

        if ($this->editingId) {
            $model = $this->findModel($this->editingId);

            $this->authorize('update', $model);

            $model->fill($payload);
            $model->save();
        } else {
            $this->authorize('create', $this->model());

            $modelClass = $this->model();

            /** @var Model $model */
            $model = $modelClass::query()->create($payload);
        }

        $this->afterSave($model);

        $this->dispatch('record-saved');

        $this->create();
    }

    public function validationAttributes(): array
    {
        return $this->validationAttributeLabels();
    }

    abstract protected function model(): string;

    abstract protected function view(): string;

    abstract protected function defaultForm(): array;

    abstract protected function formRules(): array;

    abstract protected function query(): Builder;

    abstract protected function mutateFormData(array $data): array;

    abstract protected function fillFromModel(Model $model): array;

    protected function validationAttributeLabels(): array
    {
        return [];
    }

    protected function afterSave(Model $model): void
    {
        // Allow subclasses to hook into save lifecycle.
    }

    protected function findModel(int $id): Model
    {
        $modelClass = $this->model();

        /** @var Model $model */
        $model = $modelClass::query()->findOrFail($id);

        return $model;
    }
}
