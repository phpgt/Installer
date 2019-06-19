Command line tools for installing WebEngine.
============================================

One of the main notions of development within PHP.Gt is to remove any barriers that might prevent someone from productively building a prototype. This repository intends to remove all barriers by providing a single `gt` script that contains all of the setup commands to get you going on new and existing projects.

Note: this is a totally optional part of PHP.Gt/WebEngine. You can continue to set up WebEngine projects manually without losing any functionality.

PHP.Gt relies on Composer as the installation medium. Using Composer ensures you have the correct versions of PHP and extensions installed.

With Composer installed, run `composer global require phpgt/installer` to globally install the interface required for creating new WebEngine applications. This will make the `gt` command available to your terminal, which includes the helper commands required for creating new projects and running them.

For the `gt` command to be available globally, ensure that your `~/.config/composer/vendor/bin` directory is in your PATH (or on Windows, `C:\%HOMEPATH%\AppData\Roaming\Composer\vendor\bin`).

