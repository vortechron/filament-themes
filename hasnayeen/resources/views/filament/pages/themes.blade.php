<x-filament-panels::page>
    <section class="vft-section">
        <header class="vft-section-header">
            <h2>{{ __('themes::themes.primary_color') }}</h2>
            <p>{{ __('themes::themes.select_base_color') }}</p>
        </header>

        <div class="vft-color-list">
            @if ($this->getCurrentTheme() instanceof \Hasnayeen\Themes\Contracts\HasChangeableColor)
                @foreach ($this->getColors() as $name => $color)
                    <button
                        type="button"
                        wire:click="setColor('{{ $name }}')"
                        title="{{ \Illuminate\Support\Str::title($name) }}"
                        class="vft-color-swatch {{ $this->getColor() === $name ? 'is-active' : '' }}"
                        style="background-color: {{ $color[500] }};"
                    >
                        <span class="vft-sr-only">{{ \Illuminate\Support\Str::title($name) }}</span>
                    </button>
                @endforeach

                <label class="vft-custom-color">
                    <input
                        type="color"
                        id="custom"
                        name="custom"
                        wire:change="setColor($event.target.value)"
                    >
                    <span>{{ __('themes::themes.custom') }}</span>
                </label>
            @else
                <p>{{ __('themes::themes.no_changing_primary_color') }}</p>
            @endif
        </div>
    </section>

    <section class="vft-section">
        <header class="vft-section-header">
            <h2>{{ __('themes::themes.themes') }}</h2>
            <p>{{ __('themes::themes.select_interface') }}</p>
        </header>

        <div class="vft-theme-list">
            @foreach ($this->getThemes() as $name => $theme)
                @php
                    $noLightMode = in_array(\Hasnayeen\Themes\Contracts\HasOnlyDarkMode::class, class_implements($theme));
                    $noDarkMode = in_array(\Hasnayeen\Themes\Contracts\HasOnlyLightMode::class, class_implements($theme));
                    $supportColorChange = in_array(\Hasnayeen\Themes\Contracts\HasChangeableColor::class, class_implements($theme));
                    $isActive = $this->getCurrentTheme()->getName() === $name;
                @endphp

                <x-filament::section>
                    <x-slot name="heading">
                        <span>{{ \Illuminate\Support\Str::title($name) }}</span>
                    </x-slot>

                    <x-slot name="afterHeader">
                        <x-filament::button
                            wire:click="setTheme('{{ $name }}')"
                            size="xs"
                            :outlined="! $isActive"
                            :disabled="$isActive"
                        >
                            {{ $isActive ? __('themes::themes.active') : __('themes::themes.select') }}
                        </x-filament::button>
                    </x-slot>

                    <div class="vft-theme-preview vft-theme-preview-{{ $name }}">
                        <span class="vft-preview-sidebar"></span>
                        <span class="vft-preview-card"></span>
                        <span class="vft-preview-card"></span>
                    </div>

                    <div class="vft-badges">
                        @if ($supportColorChange)
                            <x-filament::badge color="primary">
                                {{ __('themes::themes.support_changing_primary_color') }}
                            </x-filament::badge>
                        @endif

                        @if (! $noLightMode)
                            <x-filament::badge color="warning">
                                {{ __('themes::themes.light') }}
                            </x-filament::badge>
                        @endif

                        @if (! $noDarkMode)
                            <x-filament::badge color="gray">
                                {{ __('themes::themes.dark') }}
                            </x-filament::badge>
                        @endif
                    </div>
                </x-filament::section>
            @endforeach
        </div>
    </section>
</x-filament-panels::page>
