<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppInmueble extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <a href="/" wire:navigate>
                    <!-- Hidden when collapsed -->
                    <div {{ $attributes->class(["hidden-when-collapsed"]) }}>
                        <div class="flex items-center gap-2">
                            <x-avatar :image="asset('inmueble-favicon.webp')" class="!w-10" />
                            <!-- <x-icon name="s-home-modern" class="w-6 -mb-1 text-zinc-700" /> -->
                            <span class="text-3xl font-bold text-transparent me-3 bg-gradient-to-r from-zinc-700 to-zinc-500 bg-clip-text ">
                                Inmuebles
                            </span>
                        </div>
                    </div>

                    <!-- Display when collapsed -->
                    <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
                        <x-avatar :image="asset('inmueble-favicon.webp')" class="!w-10" />
                    </div>
                </a>
            HTML;
    }
}
