<?php
namespace InnoShop\Panel\Components\Data;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sorter extends Component
{
    /**
     * Available sort options
     *
     * @var array
     */
    public array $options;

    /**
     * Create a new component instance.
     *
     * @param  array  $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('panel::components.data.sorter');
    }
}
