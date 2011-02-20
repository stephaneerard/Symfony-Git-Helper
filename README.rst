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
  ./sf-git git:clone myGitRepo
  
If you want to make it a submodule for your current working git repository, declaring 
a dependency, simply add --submodule at the end.

There are other options available, like --target to overload the target defined in the .repositories file
used in the execution. 
 

Defining Git Repositories .repositories filepath
------------------------------------------------

When executing sf-git.phar, you have tree choices :

1- giving a --repositories /path/to/my/.repositories argument
2- having a .repositories file existing in current working directory
3- having an environment variable SF_GIT_REPO_DIC pointing to a .repositories -like file

 
Structure of a .repositories Git Repositories Dictionnary file
--------------------------------------------------------------

An example is given in the test-project/.repositories
It looks as follow:

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


Usage:
------

* To install :

::

  mkdir ~/.sf-git 
  cd ~/.sf-git
  curl https://github.com/stephaneerard/Symfony-Git-Helper/raw/master/bin/install | sudo sh sfgit
  
The latest line, latest word is of your choice. sfgit will be the name of the script generated in /usr/bin/.
So you'll be able to call Sf-Git from anywhere.


  
* To init your example project "my-project" :

::

  mkdir ~/projects/my-project
  cd ~/projects/my-project
  sf-git git:init #this is 'git init'



* To initialize a dependency to symfony (as defined in your .repositories) :

::

  sf-git git:clone symfony --submodule
  #this is like executing :
  # git submodule add git://github.com/stephaneerard/symfony.git src/vendor/symfony
  # cd src/vendor/symfony 
  # git remote add upstream git://github.com/symfony/symfony.git



* To overload the path

::

  sf-git git:clone symfony --submodule --path /path/where/to/clone/symfony
  #this is like executing same as above but :
  # git submodule add git://github.com/stephaneerard/symfony.git /path/where/to/clone/symfony
  #plus same as above
  
If a tag is set, it will be checked out
If both a tag and a branch are defined in the .repositories, the branch will take over.



Todo:
-----

* Add option to overload the tag/branch for CloneCommand
* Add command to pull all repositories
* Add a file to know which repositories have been created (when cloned) within a directory
* Add a mass-clone command, letting user specifying multiple repositories to clone at once

