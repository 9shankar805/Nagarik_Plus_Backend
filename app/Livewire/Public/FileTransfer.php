<?php

namespace App\Livewire\Public;

use Livewire\Component;

class FileTransfer extends Component
{
    public $pin;

    public function mount()
    {
        // Generate a PIN when the page loads
        $this->pin = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.public.file-transfer')->layout('layouts.public');
    }
}
