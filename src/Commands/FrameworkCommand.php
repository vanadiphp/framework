<?php

namespace Vanadi\Framework\Commands;

use Illuminate\Console\Command;

class FrameworkCommand extends Command
{
    public $signature = 'vanadi-framework';

    public $description = 'Vanadi Framework Command';

    public function handle(): int
    {
        $this->comment('Nothing to do');

        return self::SUCCESS;
    }
}
