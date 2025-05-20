<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class SlButton extends Component
{
    public string $buttonId;
    public string $buttonClass;
    public string $type;
    public string $style;
    public string $text;
    public string $icon;
    public string $action;

    public function __construct(
        string $type = 'button',
        string $style = 'primary',
        string $text = '',
        string $id = '',
        string $class = '',
        string $icon = '',
        string $action = ''
    ) {
        $this->type = $type;
        $this->style = $style;
        $this->text = $text;
        $this->icon = $icon;
        $this->action = $action;

        $this->buttonId = $id ?: Str::random(8);

        $defaultClasses = 'sl-btn font-semibold rounded btn btn-sm';

        switch ($style) {
            default:
            case 'primary':
                $defaultClasses .= " btn-primary sign-lingua-purple-button";
                break;
            case 'danger':
                $defaultClasses .= " btn-danger sign-lingua-red-button";
                break;
            case 'secondary':
                $defaultClasses .= " btn-secondary sign-lingua-gray-button";
                break;
        }

        $this->buttonClass = trim("$defaultClasses $class");
    }

    public function render(): View|Closure|string
    {
        return view('components.sl-button');
    }
}
