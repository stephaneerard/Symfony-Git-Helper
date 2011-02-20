<?php
require_once __DIR__.'/../../Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Se\\Git' 							=> __DIR__.'/../../',
    'Symfony'               => __DIR__.'/../../',
));
$loader->register();

