<?php

/*
 * This file is part of the Git Bundle
 *
 * (c) Stéphane Erard <stephane.erard@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Se\Git\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;
use Symfony\Component\Finder\Finder;

/**
 * Initializes a new Git project.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class InstallCommand extends Command
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setDefinition(array(
			new InputOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Where to symlink', '/usr/bin/sfgit'),
		))
		->setName('git:install')
		->setDescription('install Sf-Git system-wide')
		;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if(!strlen(\Phar::running()) > 0 )
		{
			throw new \RuntimeException('Must be running from Phar');
		}
		$path = str_replace('phar://', '', \Phar::running());
		$content = <<<EOF
#!/usr/bin/env php
<?php
require_once '{$path}';

EOF;

		file_put_contents($input->getOption('path'), $content);
		chmod($input->getOption('path'), '0007');
		$output->writeln('<info>Installed.</info>');
		$output->writeln(sprintf('<info>Usage: %s</info>', basename($input->getOption('path'))));
	}
}
