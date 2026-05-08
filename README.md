Command line installer for `gt`
===============================

This repository hosts the installer served at:

- `https://php.gt/install`

The installer sets up a `gt` command for Linux and macOS shells using either native PHP with Composer or Docker.

Quick install:

- `curl https://php.gt/install | sh`

Verbose output:

- `curl https://php.gt/install | sh -s -- --verbose`

Force shell profile selection:

- `curl https://php.gt/install | sh -s -- --shell bash`

The `gt` command itself is implemented at [PHP.GT/GtCommand](https://github.com/phpgt/GtCommand) and installed as the Composer package `phpgt/gtcommand`.

How the installer works
-----------------------

1. Preflight checks run before any prompts:
   - Native requirements: `php >= 8.4`, `curl`, and either archive support (`unzip`/`7z`/PHP `zip`) or `git`.
   - Docker availability (`docker` command).
2. If neither native nor Docker is viable, the installer explains missing requirements, suggests package-manager install commands, and links to:
   - `https://php.gt/docs/installer/environments`
3. If both paths are viable, the user chooses `native` or `docker`. If only one is viable, it is selected automatically.
4. The installer asks which shell profile to update (`bash`, `zsh`, `sh`), defaulting from:
   - `--shell` override, then parent-process inference, then `$SHELL`.
5. Native path:
   - Uses an existing `composer` executable if present; otherwise it offers to download `composer-stable.phar`.
   - Installs `phpgt/gtcommand` with `composer global require`.
6. Docker path:
   - Uses `composer:2` container with mounted Composer home.
   - Installs `phpgt/gtcommand` in that mounted Composer home.
7. In both paths, the installer creates an executable launcher named `gt` in a writable bin directory:
   - It prefers `/usr/local/bin`, with a fallback to `~/.local/bin` (or `/tmp/.local/bin` if `HOME` is unavailable).
8. If the launcher or Composer directory is not on `PATH`, the installer offers to append an export to the selected shell rc file.

Notes
-----

- By default, the installer suppresses noisy command output and shows simple progress feedback for long steps.
- Use `--verbose` for full command logs.
- `gt` remains updatable through Composer (`phpgt/gtcommand`), while the installed launcher stays as the stable entry point.
