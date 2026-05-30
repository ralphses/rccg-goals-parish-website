<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventDetail extends Component
{
    public $title;
    public $description;
    public $keywords;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $title = null,
        $description = null,
        $keywords = null
    ) {
        $this->title = $title ?? 'Church Event | The Redeemed Christian Church of God, GOALS Parish';

        $this->description = $description ?? 
            'Join us at The Redeemed Christian Church of God, GOALS Parish for inspiring worship services, special programs, and impactful church events designed to strengthen your faith.';

        $this->keywords = $keywords ?? 
            'RCCG GOALS Parish Event, Church Program, Christian Worship Event, Gospel Gathering, Church Service in Nigeria';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.guest.event-detail');
    }
}
