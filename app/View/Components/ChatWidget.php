<?php

namespace App\View\Components;

use App\Models\Conversation;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ChatWidget extends Component
{
    public Conversation $conversation;

    /**
     * Create a new component instance.
     *
     * @param  \App\Models\Conversation  $conversation
     * @return void
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chat-widget');
    }
}