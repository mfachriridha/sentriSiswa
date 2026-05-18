<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component
{
    public function mount(): void
    {
        $this->js(<<<'JS'
            $flux.appearance = 'light';
        JS);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Theme selalu light mode')">
        <div class="text-sm text-zinc-500">Light mode aktif secara default.</div>
    </x-pages::settings.layout>
</section>
