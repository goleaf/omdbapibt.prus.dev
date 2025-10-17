<div class="space-y-4">
    @forelse ($reviews as $review)
        <article class="rounded-lg bg-white p-6 shadow">
            <header class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $review->movie?->localizedTitle() ?? __('reviews.labels.unknown_movie') }}</h3>
                    <p class="text-sm text-gray-500">Reviewed by {{ $review->user->name }} &bull; Rated {{ $review->rating }} / 5</p>
                </div>
                <time class="text-xs text-gray-400" datetime="{{ $review->created_at->toIso8601String() }}">
                    {{ $review->created_at->diffForHumans() }}
                </time>
            </header>

            <div class="prose prose-sm mt-4 max-w-none">
                {!! $review->sanitized_body !!}
            </div>
        </article>
    @empty
        <p class="text-sm text-gray-500">No reviews have been submitted yet.</p>
    @endforelse

    <div>
        {{ $reviews->links() }}
    </div>
</div>
