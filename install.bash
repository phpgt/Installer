#!/bin/bash
# This is the installation script for PHP.Gt/CliTools - a set of tools for
# creating and managing WebEngine applications.
# Read the documentation at https://www.php.gt/clitools or continue reading
# here to learn the story of what's happening here.

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
# 3) Check that Docker is installed. If so, we can ask the developer whether
# they want to use Docker for development. If so, there's no need to install
# other dependencies - but we don't want to force the developer to use Docker
# if they're not familiar with it.
# 4) Check that Composer is available. Composer is the tool we use to manage
# dependencies throughout PHP.Gt, so once this and PHP are available, we're
# good to go!
# 5) Check that Composer exposes the `gt` command globally. Whether this has
# been done using native binaries or via Docker, the `gt` command needs to be
# available globally in the developer's terminal. This usually means by adding
# something to the PATH variable.
#
# Okay, let's get started.
#
# Step 1: Let's see if the developer already has `gt` on their system, because
# if they do, we don't need to do anything else.
if command -v gt &> /dev/null
then
	first_line=$(gt 2>&1 | head -n 1)
# Here, the system already has a `gt` command available, but we'll check the
# first line to make sure it's PHP.Gt.
	if [[ "$first_line" == *"PHP.Gt"* ]]
	then
		echo "Your system is already set up and ready to run!"
		echo "Type 'gt help' for more information."
		echo "To make sure you're running the most up-to-date version, type 'gt update'"
		echo "For help, please visit: https://www.php.gt/gt-command"
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
# for `php` or `docker` and ask the developer what they want to do.
