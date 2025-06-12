<x-filament::page class="flex justify-center items-center h-full">
    
        <x-filament::card class="w-2/5">
            <div>
                <h1 class="text-2xl font-bold mb-4">{{ $this->getTitle() }}</h1>
                <div>
                    {{ $this->form }}
                </div>
            </div>
            <br>
            <div class="flex justify-center">
                <x-filament::button wire:click="submit" class="w-1/2 mt-4">
                    Acceder
                </x-filament::button>
            </div>
        </x-filament::card>

</x-filament::page>
