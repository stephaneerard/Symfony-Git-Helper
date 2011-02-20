Symfony Git Helper
==================

Symfony Git Helper will help you manage your project and its Git dependencies.

You can declare in a .repositories file all your repositories, then call
this application to initialize your git dependencies.

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

