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
		->addOption('target', 'ta', InputOption::VALUE_OPTIONAL, 'The target directory', false)
		->addOption('submodule', 's', InputOption::VALUE_NONE, 'To-clone repository will be added as a submodule')
		->addOption('commit', 'c', InputOption::VALUE_NONE, 'Commit the repository')
		->addOption('commit-message', 'm', InputOption::VALUE_OPTIONAL, 'Commit message', '')
		->addOption('branch', 'b', InputOption::VALUE_OPTIONAL, 'Branch to checkout', false)
		->addOption('tag', 't', InputOption::VALUE_OPTIONAL, 'Tag to checkout', false)
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
		
		$repositoryDef['branch'] = $input->getOption('branch') === false ? $repositoryDef['branch'] : $input->getOption('branch');
		$repositoryDef['tag'] = $input->getOption('tag') === false ? (isset($repositoryDef['tag']) ? $repositoryDef['tag'] : false) : $input->getOption('tag');
		
		$commit = $input->getOption('commit');
		$commitMessage = 
			$input->getOption('commit-message') === '' ? 
				sprintf(
					'Commit %s %s', 
					$input->getOption('submodule') ? 'submodule' : 'clone', 
					$input->getArgument('name')
				) : $input->getOption('commit-message');
		
		
		
		$this->_clone($repositoryDef['url'], $target, $input->getOption('submodule'), $output);
		$originalDir = getcwd();
		chdir(realpath('./'.$target));

		
		if(isset($repositoryDef['remotes']) && !empty($repositoryDef['remotes']))
		{
			$this->_remotes($repositoryDef['remotes'], $output);
		}

		if(isset($repositoryDef['branch']) && $repositoryDef['branch'])
		{
			$this->_branch($repositoryDef['branch'], $output);
		}
		elseif(isset($repositoryDef['tag']) && $repositoryDef['tag'])
		{
			$this->_tag($repositoryDef['tag'], $output);
		}
		
		if($commit)
		{
			$this->_commit($commitMessage, $originalDir, $output);
		}

	}

	/**
	 * Clone $url repository to $target.
	 * If $submodule, will be executed as a git submodule command
	 * 
	 * @param unknown_type $url
	 * @param unknown_type $target
	 * @param unknown_type $submodule
	 * @param unknown_type $output
	 */
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

	/**
	 * Add $remotes
	 * 
	 * @param unknown_type $remotes
	 * @param unknown_type $output
	 */
	protected function _remotes($remotes, $output)
	{
		foreach($remotes as $remote => $def)
		{
			$output->writeln(sprintf('<info>adding remote "%s" (%s)</info>', $remote, $def['url']));
			exec(sprintf('git remote add %s %s', $remote, $def['url']));
		}
	}

	/**
	 * Will checkout $branch
	 * 
	 * @param unknown_type $branch
	 * @param unknown_type $output
	 */
	protected function _branch($branch, $output)
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

	/**
	 * Will checkout $tag
	 * 
	 * @param unknown_type $tag
	 * @param unknown_type $output
	 */
	protected function _tag($tag, $output)
	{
		$output->writeln(sprintf('<info>branching to tag "%s"</info>', $tag));
		exec(sprintf('git checkout -b %s tags/%s', $tag, $tag));
	}
	
	protected function _commit($msg, $dir, $output)
	{
		exec(sprintf('git add ./'));
		$output->writeln(sprintf('<info> committing...</info>'));
		chdir($dir);
		exec(sprintf('git commit -m"%s"', $msg));
	}
}
