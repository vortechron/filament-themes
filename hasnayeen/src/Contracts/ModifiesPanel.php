<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Contracts;

use Filament\Panel;

interface ModifiesPanel
{
    public function modifyPanel(Panel $panel): void;
}
