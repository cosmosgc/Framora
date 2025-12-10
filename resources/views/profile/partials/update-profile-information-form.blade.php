<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Informação de perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Atualize as informações do seu perfil e o seu endereço de e-mail.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- Note: enctype required for file uploads --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Seu endereço de e-mail não foi verificado.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Clique aqui para reenviar o e-mail de verificação..') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Um novo link de verificação foi enviado para o seu endereço de e-mail..') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avatar field --}}
        <div class="space-y-2">
            <x-input-label for="avatar" :value="__('Avatar')" />
            <div class="flex items-center gap-4">
                {{-- current avatar preview (if exists) --}}
                <div>
                    @if ($user->avatar)
                        <img id="avatarPreview" src="{{ asset($user->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border" />
                    @else
                        {{-- placeholder --}}
                        <div id="avatarPreview" class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center border">
                            <span class="text-xs text-gray-500">No avatar</span>
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                    <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Image types only.</p>
                </div>
            </div>
        </div>

        {{-- Bio field --}}
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border rounded-md p-2" maxlength="1000">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            <p class="text-xs text-gray-500 mt-1">Biografia curta (opcional, até 1000 letras).</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        // Simple avatar preview
        (function () {
            const input = document.getElementById('avatar');
            const preview = document.getElementById('avatarPreview');

            if (!input || !preview) return;

            input.addEventListener('change', function (e) {
                const file = this.files && this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (ev) {
                    // if preview is img element, change src; otherwise replace content with an img
                    if (preview.tagName.toLowerCase() === 'img') {
                        preview.src = ev.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.src = ev.target.result;
                        img.alt = 'Avatar';
                        img.className = 'w-20 h-20 rounded-full object-cover border';
                        preview.replaceWith(img);
                    }
                };
                reader.readAsDataURL(file);
            });
        })();
    </script>
</section>
