<?php

namespace App\View\Components;

use App\Support\PremiumAccess;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LockedField extends Component
{
    public bool $isLocked;

    /**
     * Create a new component instance.
     */
    public function __construct(public ?string $value = null, ?bool $forceLocked = null)
    {
        $this->isLocked = $forceLocked ?? ! PremiumAccess::isPremium(auth()->user());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.locked-field');
    }
}
