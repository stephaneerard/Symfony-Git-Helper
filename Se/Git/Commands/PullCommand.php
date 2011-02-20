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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Se\Git\Application;
use Se\Git\Commands\Base\GitCommand as BaseGitCommand;

/**
 * Pull a Git Repository.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class PullCommand extends BaseGitCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		parent::configure()
		->addArgument('name', InputArgument::REQUIRED, 'The Git Repository name')
		
		->addOption('target', 				'ta', InputOption::VALUE_OPTIONAL, 	'The target directory', false)
		->addOption('remote', 				's', 	InputOption::VALUE_OPTIONAL, 	'The remote to use', false)
		->addOption('branch', 				'c', 	InputOption::VALUE_OPTIONAL, 	'The branch on remote', false)
		
		->setName('git:pull')
		->setDescription('Pull a Git Repository')
		->setAliases(array('c'))
		;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		parent::execute($input, $output);
		$repositoryDef = $this->getRepositoryDefinition($input->getArgument('name'));
		
		$target = $input->getOption('target') === false ? $repositoryDef['target'] : $input->getOption('target');
		
		$repositoryDef['remote'] = $input->getOption('remote') === false ? false : $input->getOption('remote');
		if(false === $repositoryDef['remote'])
		{
			//check if repo['branch'] doesnt contain any /
			if(strpos($repositoryDef['branch'], '/') !== false)
			{
				list($repositoryDef['remote'], $repositoryDef['branch']) = explode('/', $repositoryDef['branch']);
			}else{
				$repositoryDef['remote'] = 'origin';
			}
		}
		$repositoryDef['branch'] = $input->getOption('branch') === false ? $repositoryDef['branch'] : $input->getOption('branch');
		

		chdir($target);
		$cmd = sprintf('git pull %s %s', $repositoryDef['remote'], $repositoryDef['branch']);
		$output->writeln('<info>' . $cmd . '</info>');
		exec($cmd);
	}
}
