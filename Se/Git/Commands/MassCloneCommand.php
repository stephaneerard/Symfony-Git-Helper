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

use Symfony\Component\Console\Input\ArgvInput;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Se\Git\Application;
use Se\Git\Commands\Base\GitCommand as BaseGitCommand;

/**
 * Clone a Git Repository.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class MassCloneCommand extends BaseGitCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		parent::configure()
		->addArgument('names', InputArgument::REQUIRED, 'The Git Repository names')

		->addOption('submodule', 's', InputOption::VALUE_NONE, 'Clone repository and make it a submodule')
		->addOption('commit', 'c', InputOption::VALUE_NONE, 'Commit the changes')
		->addOption('commit-message', 'm', InputOption::VALUE_OPTIONAL, 'Commit message')

		->setName('git:mclone')
		->setDescription('Clone Git Repositories')
		->setAliases(array('mc'))
		;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		parent::execute($input, $output);
		$names = explode(' ', $input->getArgument('names'));

		$clone = new CloneCommand();
		foreach($names as $name)
		{
			$args = array('git:clone');
			if($input->getOption('submodule'))
			{
				$args[] = '--submodule';
			}
			if($input->getOption('commit'))
			{
				$args[] = '--commit';
			}
			if($input->getOption('commit-message'))
			{
				$args[] = '--commit-message';
			}
			$args[] = $name;
			$cloneInput = new ArgvInput($args);
	  	$clone->run($cloneInput, $output);
		}
	}
}
