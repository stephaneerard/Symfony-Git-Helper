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
 * Clone a Git Repository.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class CloneCommand extends BaseGitCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		parent::configure()
		->addArgument('name', InputArgument::REQUIRED, 'The Git Repository name')
		->addOption('target', 't', InputOption::VALUE_OPTIONAL, 'The target directory', false)
		->addOption('submodule', 's', InputOption::VALUE_NONE, 'Clone repository and make it a submodule')
		->setName('git:clone')
		->setDescription('Clone a Git Repository')
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

		$this->_clone($repositoryDef['url'], $target, $input->getOption('submodule'), $output);

		chdir(realpath('./'.$target));

		if(isset($repositoryDef['remotes']) && !empty($repositoryDef['remotes']))
		{
			$this->_remotes($target, $repositoryDef['remotes'], $output);
		}

		if(isset($repositoryDef['branch']) && $repositoryDef['branch'])
		{
			$this->_branch($target, $repositoryDef['branch'], $output);
		}
		elseif(isset($repositoryDef['tag']) && $repositoryDef['tag'])
		{
			$this->_tag($target, $repositoryDef['tag'], $output);
		}

	}

	protected function _clone($url, $target, $submodule, $output)
	{
		$cmd = sprintf(
			'git %s %s %s %s',
		$submodule ? 'submodule add' : 'clone',
		$url,
		$target,
		$submodule ? '--recursive' : ''
		);
		$output->writeln(sprintf('<info>%s</info>', $cmd));
		exec($cmd, $out, $exitCode);
	}

	protected function _remotes($target, $remotes, $output)
	{
		foreach($remotes as $remote => $def)
		{
			$output->writeln(sprintf('<info>adding remote "%s" (%s)</info>', $remote, $def['url']));
			exec(sprintf('git remote add %s %s', $remote, $def['url']));
		}
	}

	protected function _branch($target, $branch, $output)
	{
		if(strpos('/', $branch))
		{
			list($remote, $branch) = explode('/', $branch);
			if($branch === null){
				$branch = $remote;
				$remote = 'origin';
			}
		}else{
			$remote = 'origin';
		}
		if($branch === 'master' && $remote === 'origin') return;
		$output->writeln(sprintf('<info>branching to "%s" from remote "%s"</info>', $branch, $remote));
		exec(sprintf('git checkout -b %s %s/%s', $branch, $remote, $branch));
	}

	protected function _tag($target, $tag, $output)
	{
		$output->writeln(sprintf('<info>branching to tag "%s"</info>', $tag));
		exec(sprintf('git checkout -b %s tags/%s', $tag, $tag));
	}
}
