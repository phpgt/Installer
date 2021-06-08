#!/bin/bash
# This is the installation script for PHP.Gt/CliTools - a set of tools for
# creating and managing WebEngine applications.
# Read the documentation at https://www.php.gt/clitools or continue reading
# here to learn the story of what's happening here.
#
# Run this script: bash <( curl https://install.php.gt )
#
# One of the main notions of development within PHP.Gt is to remove any barriers
# that might prevent someone from productively building a prototype.
#
# The shell script you're reading now is intended to be executed from the
# command line of a developer's computer who wants to get started with WebEngine
# as quickly as possible. The idea is for the script to check the environment
# and help the developer install any top-level dependencies like programming
# languages, by providing human feedback rather than "ERROR 105: PHP-CLI <= 7.4
# IS DEPRECATED", for example.
#
# So, let's get started shall we? Let's outline the steps we're going to take
# first, and then walk through the process of getting the developer's system
# set up.
#
# Steps:
# 1) Check to see if `gt` is already installed. If so, there's no need to do
# anything else, apart from let the developer know in a friendly way.
# 2) Check that PHP is installed at the correct version or...
# 3) Check that Composer is available. Composer is the tool we use to manage
# dependencies throughout PHP.Gt, so once this and PHP are available, we're
# good to go!
# 4) Check that Composer exposes the `gt` command globally. The `gt` command
# needs to be available globally in the developer's terminal. This usually
# means by adding something to the PATH variable.
#
# Okay, let's get started.
# Here's a helper function for checking if a command exists.
command_exists () {
	command -v "$1" &> /dev/null
}

echo
echo "Running the PHP.Gt installation script..."
echo
sleep 1
#
# Step 1: Let's see if the developer already has `gt` on their system, because
# if they do, we don't need to do anything else.
if command_exists gt
then
	first_line=$(gt 2>&1 | head -n 1)
# Here, the system already has a `gt` command available, but we'll check the
# first line to make sure it's PHP.Gt.
	if [[ "$first_line" == *"PHP.Gt"* ]]
	then
		echo "Your system is already set up and ready to run!"
		echo "Type 'gt help' for more information."
		echo "To make sure you're running the most up-to-date version, type 'gt update'."
		echo "For help, please visit: https://www.php.gt/clitools/gt-command"
		echo "Have fun!"
		exit
	else
		echo "Your system already has a 'gt' command installed, "
		echo "but it's not supplied by PHP.Gt."
		echo "Here's what's already installed:"
		which gt
		echo "For help, please visit: "
		echo "https://www.php.gt/installer/existing-gt-command"
		exit 100
	fi
fi

# At this point, the developer doesn't have `gt` installed, so we need to check
# for `php` and `composer`, and ask the developer what they want to do.
php_version="0"
if command_exists php
then
	php_version=$(php -r "echo PHP_MAJOR_VERSION;")
fi

composer_version="0"
if command_exists composer
then
	composer_version="$(composer --version | cut -d" " -f 3 | cut -d"." -f 1)"
fi

if (( php_version >= 8 )) && (( composer_version >= 4 ))
then
	echo "Looks like you have PHP $php_version and Composer $composer_version installed - great!"
	echo "Press any key to require the PHP.Gt/CliTools globally using Composer..."
	read -r
	composer global require phpgt/installer

	echo
	echo -n "Composer has completed installing PHP.Gt/CliTools successfully "

	if command_exists gt
	then
		echo "and you've now got the 'gt' command available in your terminal!"
		echo
		echo "Type 'gt help' or visit https://www.php.gt/clitools/gt-command for more information."
		echo
	else
		echo "but you need to add Composer's global directory to your PATH."
		echo
		echo "For a tutorial, please visit https://www.php.gt/clitools/composer-path"
		echo
	fi

	exit
else
	echo "For PHP.Gt to work, you need at least PHP 8 and Composer installed on your computer, but it doesn't look like you have these installed yet."
	echo "Here's a guide that helps you get set up, please visit:"
	echo "https://www.php.gt/webengine/environment-setup"
fi
