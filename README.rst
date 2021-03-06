Symfony Git Helper
==================

Symfony Git Helper will help you manage your project and its Git dependencies.

You can declare in a .repositories file all your repositories, then call
this application to initialize your git dependencies.

The real goal of this is to let you declare all your git repositories in one file,
name them, then use these names to init your projects with these dependencies.

Instead of executing 
::
  git clone /path/to/my/git/repo
  
You'll be able to execute

::

  sfgit git:clone myGitRepo
  
If you want to make it a submodule for your current working git repository, declaring 
a dependency, simply add --submodule at the end.

There are other options available, like --target to overload the target defined in the .repositories file
used in the execution. 
 

Defining Git Repositories .repositories filepath
------------------------------------------------

When executing sf-git.phar, you have tree choices :

* Giving a --repositories /path/to/my/.repositories argument (if none given, then)
* Having a .repositories file existing in current working directory (if none exists, then)
* Having an environment variable SF_GIT_REPO_DIC pointing to a .repositories -like file (if none exists, throw exception)

These are cited in the order the code executes.
 
Structure of a .repositories Git Repositories Dictionnary file
--------------------------------------------------------------

An example is given in the test-project/.repositories
It looks as follow (this is YAML syntax) :

::

  symfony:
    url: git://github.com/stephaneerard/symfony.git
    remotes:
      upstream:
        url: git://github.com/symfony/symfony.git
    branch: master
    target: src/vendor/symfony
  
  doctrine:
    url: git://github.com/stephaneerard/doctrine2.git
    remotes:
      upstream:
        url: git://github.com/doctrine/doctrine2.git
    tag: 2.0.1
    target: src/vendor/doctrine
  
  imagine:
    url: git://github.com/stephaneerard/Imagine.git
    remotes:
      upstream:
        url: git://github.com/avalanche123/Imagine.git
    target: src/vendor/imagine
    branch: origin/draw_text


* symfony: {url: } will be the "origin" remote repository.

I can add remotes too, like "upstream" (see Git conventions of use; same for "origin"). 
The remotes will be automatically added to the submdule/clone repository

* "target" is the actual directory target to use in any project where you'll clone the repository.
Basically, I want imagine repository to be cloned, each time, in the very same src/vendor/imagine folder.
You can overload this by adding --target to the git:clone command.

* "branch" is the branch to checkout. If specified, the branch will be checked out but not committed.
Add --commit to commit the checkout, and -m to specify the commit message.

* "tag" is the tag to checkout. If specified, the tag will be checkout out but not committed.
Add --commit to commit the checkout, and -m to specify the commit message. If a branch AND a tag are specified
in the .repositories file, but none is overloaded by command-line, the branch will take over.


Usage:
------

::

  git:init 			Initialize a new git repository

  git:clone 			Clones a repository.
    [--repositories] 		The .repositories filepath
    [--submodule]  		Add a new submodule
    [--commit]     		Commit the changes when using --submodule and we are in a git repository
    [--commit-message]		Commit message
    [--branch]			Branch to checkout, overloads the one defined in --repositories file, if any
    [--tag]			Tag to checkout, overloads the one defined in --repositories file, if any
    [--target]			Path target where to clone/submodule add the repository, overloads the one defined in --repositories file, if any

  git:mclone			Massively clone repositories
    [--repositories]		The .repositories filepath
    [--commit]			Commit the changes when using --submodule and we are in a git repository
    [--commit-message]		Commit message

  git:pull			Pull a git repository
    [--repositories]		The .repositories filepath
    [--target]			The path target of the repository, overloads --repositories definition
    [--remote]			The remote to use, overloads --repositories definition
    [--branch]			The branch on remote to use, overloads --repositories definition


  git:mpull			Massively pull repositories
    [--repositories]		The .repositories filepath
    names			The names of the repositories to pull


* To install :

::

  mkdir ~/.sf-git 
  cd ~/.sf-git
  curl https://github.com/stephaneerard/Symfony-Git-Helper/raw/master/bin/install | sudo sh sfgit
  
The latest line, latest word is of your choice. sfgit will be the name of the script generated in /usr/bin/.
So you'll be able to call Sf-Git from anywhere.

The script will be executed as it is piped, you'll have to enter your password when the download has finished.

  
* To init your example project "my-project" :

::

  mkdir ~/projects/my-project
  cd ~/projects/my-project
  sfgit git:init #this is 'git init'



* To initialize a dependency to symfony (as defined in your .repositories) :

::

  sfgit git:clone symfony --submodule

  * This is like executing :

    git submodule add git://github.com/stephaneerard/symfony.git src/vendor/symfony
    cd src/vendor/symfony 
    git remote add upstream git://github.com/symfony/symfony.git



* To overload the path

::

  sfgit git:clone symfony --submodule --path /path/where/to/clone/symfony
 
  * This is like executing same as above but :

    git submodule add git://github.com/stephaneerard/symfony.git /path/where/to/clone/symfony
    #plus adding upstream, etc
  
If a tag is set, it will be checked out
If both a tag and a branch are defined in the .repositories, the branch will take over.

* To mass-clone :

::

  sfgit git:mclone --submodule --commit "symfony doctrine"

This will clone (or add as submodule when specifying --submodule) each repository named in the "".
Each submodule add or cloning will be committed using the --commit.
You can specify a commit message with --commit-message.



Todo:
-----

* Add command to pull all repositories
* Add a file to know which repositories have been created (when cloned) within a directory

