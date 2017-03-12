<?php

use Anastaszor\Incapsula\IncapsulaDomFactory;

require __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$factory = new IncapsulaDomFactory();

$dom = $factory->getDomFromUrl('https://www.cardhoarder.com/cards/');

$dom->dump();
