#!/usr/bin/env php
<?php
exec('wget https://github.com/stephaneerard/Symfony-Git-Helper/blob/master/sf-git.phar');
exec('php sf-git.phar git:install');