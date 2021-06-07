Installs PHP.Gt/CliTools for gt command access.
===============================================

One of the main notions of development within PHP.Gt is to remove any barriers that might prevent someone from productively building a prototype. This repository intends to remove the first barrier to getting started by providing a single convenience script to install access to the `gt` commands exposed by [PHP.Gt/CliTools][cli-tools].

To run the installer, paste the following into your terminal shell:

```bash
bash <( curl https://install.php.gt )
```

Note: this is a totally optional part of PHP.Gt/WebEngine. You can continue to set up WebEngine projects manually without losing any functionality.

PHP.Gt relies on Composer as the installation medium. Using Composer ensures you have the correct versions of PHP and extensions installed.

[cli-tools]: https://www.php.gt/clitools
