<div class="space-y-4">
    <form wire:submit.prevent="submit" class="space-y-4 bg-white p-6 shadow rounded-lg">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700" for="movieTitle">Movie title</label>
            <input
                id="movieTitle"
                type="text"
                wire:model.defer="form.movieTitle"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Enter the movie title"
            />
            @error('form.movieTitle')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700" for="rating">Rating</label>
            <select
                id="rating"
                wire:model.defer="form.rating"
                class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }} / 5</option>
                @endfor
            </select>
            @error('form.rating')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700" for="body">Review</label>
            <textarea
                id="body"
                wire:model.defer="form.body"
                rows="6"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Share your thoughts about the movie"
            ></textarea>
            @error('form.body')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Submit review
            </button>

            @if ($statusMessage !== '')
                <p class="text-sm text-green-600">{{ $statusMessage }}</p>
            @endif
        </div>
    </form>
</div>
