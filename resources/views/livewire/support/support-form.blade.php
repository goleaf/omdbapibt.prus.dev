<div class="space-y-6">
    <div class="rounded-3xl bg-slate-900/60 p-8 shadow-lg ring-1 ring-white/5 backdrop-blur">
        <h2 class="text-2xl font-semibold text-slate-50">{{ __('support.form.heading') }}</h2>
        <p class="mt-2 text-sm text-slate-300">{{ __('support.form.description') }}</p>

        <form wire:submit.prevent="submit" class="mt-6 space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="support-name" class="block text-sm font-medium text-slate-200">
                        {{ __('support.form.fields.name.label') }}
                    </label>
                    <input
                        id="support-name"
                        type="text"
                        wire:model.defer="name"
                        placeholder="{{ __('support.form.fields.name.placeholder') }}"
                        class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/60"
                    />
                    @error('name')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="support-email" class="block text-sm font-medium text-slate-200">
                        {{ __('support.form.fields.email.label') }}
                    </label>
                    <input
                        id="support-email"
                        type="email"
                        wire:model.defer="email"
                        placeholder="{{ __('support.form.fields.email.placeholder') }}"
                        class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/60"
                    />
                    @error('email')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="support-subject" class="block text-sm font-medium text-slate-200">
                    {{ __('support.form.fields.subject.label') }}
                </label>
                <input
                    id="support-subject"
                    type="text"
                    wire:model.defer="subject"
                    placeholder="{{ __('support.form.fields.subject.placeholder') }}"
                    class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/60"
                />
                @error('subject')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="support-message" class="block text-sm font-medium text-slate-200">
                    {{ __('support.form.fields.message.label') }}
                </label>
                <textarea
                    id="support-message"
                    rows="6"
                    wire:model.defer="message"
                    placeholder="{{ __('support.form.fields.message.placeholder') }}"
                    class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-slate-100 placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/60"
                ></textarea>
                @error('message')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-emerald-950 shadow-lg shadow-emerald-500/40 transition hover:bg-emerald-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
                >
                    {{ __('support.form.actions.submit') }}
                </button>

                <div class="text-sm text-emerald-300" wire:transition>
                    @if ($statusMessage !== '')
                        <p>{{ $statusMessage }}</p>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
