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
			new InputOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Where to symlink', '/usr/bin/sf-git'),
		))
		->setName('git:install')
		->setDescription('install Sf-Git in /usr/bin using symlink')
		;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$filesystem = new Filesystem();
		$filesystem->symlink(realpath(__DIR__ . '/../../../sf-git'), $input->getOption('path'));
	}
}
