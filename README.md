Command line installer for `gt`
===============================

This repository hosts the install script served at:

- `https://php.gt/install`

The installer sets up a `gt` command on Linux/macOS shells using either native PHP+Composer or Docker.

Quick install:

- `curl https://php.gt/install | sh`

Verbose output:

- `curl https://php.gt/install | sh -s -- --verbose`

Force shell profile selection:

- `curl https://php.gt/install | sh -s -- --shell bash`

The `gt` functionality is implemented in [phpgt/GtCommand](https://github.com/phpgt/GtCommand) and installed as Composer package `phpgt/gtcommand`.

How the installer works
-----------------------

1. Preflight checks run first (before prompts):
   - Native requirements: `php >= 8.4`, `curl`, and either archive support (`unzip`/`7z`/PHP `zip`) or `git`.
   - Docker availability (`docker` command).
2. If neither native nor Docker is viable, the installer explains missing requirements, suggests package-manager install commands, and links to:
   - `https://php.gt/docs/installer/environments`
3. If both paths are viable, user chooses `native` or `docker`. If only one is viable, it is selected automatically.
4. Installer asks which shell profile to update (`bash`, `zsh`, `sh`), defaulting from:
   - `--shell` override, then parent-process inference, then `$SHELL`.
5. Native path:
   - Uses existing `composer` if present, otherwise downloads `composer-stable.phar`.
   - Installs `phpgt/gtcommand` with Composer global require.
6. Docker path:
   - Uses `composer:2` container with mounted Composer home.
   - Installs `phpgt/gtcommand` in that mounted Composer home.
7. In both paths, installer creates a real executable launcher named `gt` in a writable bin dir:
   - prefers `/usr/local/bin`, fallback `~/.local/bin` (or `/tmp/.local/bin`).
8. If launcher/composer directories are not on `PATH`, installer offers to append exports to the selected shell rc file.

Notes
-----

- Default mode is quiet (suppresses noisy command output) and shows simple progress feedback for long steps.
- Use `--verbose` for full command logs.
- `gt` stays updatable via Composer (`phpgt/gtcommand`), while the installed `gt` launcher remains a stable entrypoint.
