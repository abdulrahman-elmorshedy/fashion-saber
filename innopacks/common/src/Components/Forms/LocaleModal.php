<?php
namespace InnoShop\Common\Components\Forms;

use Illuminate\View\Component;

class LocaleModal extends Component
{
    public string $modalId;

    public string $inputPrefix;

    public string $title;

    public function __construct(
        string $modalId = 'localeModal',
        string $inputPrefix = 'locale-input',
        string $title = '',
    ) {
        $this->modalId     = $modalId;
        $this->inputPrefix = $inputPrefix;
        $this->title       = $title;
    }

    public function render(): mixed
    {
        return view('common::components.form.locale-modal');
    }
}
