<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    public $title;
    public $value;
    public $icon;
    public $color;

    public function __construct($title = 'Dashboard Card', $value = null, $icon = 'bi bi-card-text', $color = 'bg-blue-500')
    {
        $this->title = $title;
        $this->value = $value ?? 'N/A';
        $this->icon = $icon;
        $this->color = $color;
    }

    public function render()
    {
        return view('components.dashboard-card');
    }
}
