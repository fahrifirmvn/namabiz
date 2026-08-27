<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class PremiumModal extends Component
{
    public bool $show = false;

    public string $documentTitle = '';

    #[On('open-premium-modal')]
    public function open(string $documentTitle): void
    {
        $this->documentTitle = $documentTitle;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.premium-modal');
    }
}
