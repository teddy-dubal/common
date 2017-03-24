<?php

namespace App\Common\Command;

use Pimple;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{

    protected $c;

    /**
     * Inject container
     */
    public function setContainer(Pimple $c)
    {
        $this->c = $c;
        return $this;
    }

}
