#!/bin/bash
# 1) Check required software available: php, git, composer, docker:
INSTALLED_DOCKER=0
INSTALLED_COMPOSER=0
INSTALLED_PHP=0
INSTALLED_GIT=0

if [ "$(command -v docker)" ]
then
				INSTALLED_DOCKER=1
fi

if [ "$(command -v composer)" ]
then
				INSTALLED_COMPOSER=1
fi

if [ "$(command -v php)" ]
then
				INSTALLED_PHP=1
fi

if [ "$(command -v git)" ]
then
				INSTALLED_GIT=1
fi

if [ ]

case "$OSTYPE" in
	linux*)
					;;
	darwin*)
					;;
	msys*)

esac
#Prefer to do everything through docker if available, but give option to user
#Add composer bin directory to PATH if not already
#Global composer install
