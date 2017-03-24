<?php

use App\Common\Command\GenerateDbModelCommand;
use Symfony\Component\Console\Application;

$console = new Application('App Cmd Cli', '2.0.0');
$console->add(new GenerateDbModelCommand());
$console->run();
