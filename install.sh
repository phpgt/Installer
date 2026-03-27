#!/usr/bin/env sh

DOCS_URL="https://php.gt/docs/installer"
ENV_DOCS_URL="https://php.gt/docs/installer/environments"
PACKAGE_NAME="phpgt/gtcommand"
MIN_PHP_MAJOR=8
MIN_PHP_MINOR=4
DEFAULT_COMPOSER_HOME="${HOME}/.config/composer"
LOG_FILE="${TMPDIR:-/tmp}/phpgt-installer.log"

VERBOSE=0
SHELL_OVERRIDE=""
SELECTED_SHELL=""
SHELL_RC_FILE=""
COMPOSER_CMD=""
COMPOSER_NEEDS_PHP=0
COMPOSER_HOME_DIR=""
COMPOSER_BIN_DIR=""
COMPOSER_SHIM_DIR=""
GT_LAUNCHER_PATH=""
HAS_DOCKER=0

NATIVE_PHP_OK=0
NATIVE_CURL_OK=0
NATIVE_ARCHIVE_OK=0
NATIVE_GIT_OK=0
NATIVE_OK=0
DOCKER_OK=0

say() {
	printf '%s\n' "$1"
}

warn() {
	printf '%s\n' "$1" >&2
}

have_command() {
	command -v "$1" >/dev/null 2>&1
}

check_existing_gt() {
	if have_command gt; then
		existing_gt_path="$(command -v gt 2>/dev/null)"
		say "PhpGt is already installed."
		if [ -n "$existing_gt_path" ]; then
			say "Existing gt command: ${existing_gt_path}"
		fi
		say "No changes were made."
		exit 0
	fi
}

can_prompt() {
	[ -r /dev/tty ] && [ -w /dev/tty ]
}

run_quiet() {
	if [ "$VERBOSE" -eq 1 ]; then
		"$@"
	else
		"$@" >>"$LOG_FILE" 2>&1
	fi
}

run_composer() {
	if [ "$COMPOSER_NEEDS_PHP" -eq 1 ]; then
		php "$COMPOSER_CMD" "$@"
	else
		"$COMPOSER_CMD" "$@"
	fi
}

run_composer_quiet() {
	if [ "$VERBOSE" -eq 1 ]; then
		run_composer "$@"
	else
		run_composer "$@" >>"$LOG_FILE" 2>&1
	fi
}

run_with_feedback() {
	label="$1"
	shift

	if [ "$VERBOSE" -eq 1 ]; then
		say "$label"
		"$@"
		return $?
	fi

	say "$label"
	printf '%s' "..."
	"$@" >>"$LOG_FILE" 2>&1 &
	cmd_pid=$!

	while kill -0 "$cmd_pid" 2>/dev/null; do
		sleep 5
		if kill -0 "$cmd_pid" 2>/dev/null; then
			printf '%s' "."
		fi
	done

	wait "$cmd_pid"
	cmd_status=$?
	printf '\n'
	return "$cmd_status"
}

parse_args() {
	while [ "$#" -gt 0 ]; do
		arg="$1"
		case "$arg" in
			-v|--verbose)
				VERBOSE=1
				;;
			--shell)
				shift
				if [ "$#" -gt 0 ]; then
					SHELL_OVERRIDE="$1"
				fi
				;;
			--shell=*)
				SHELL_OVERRIDE="${arg#--shell=}"
				;;
		esac
		shift
	done
}

init_log() {
	if [ "$VERBOSE" -eq 0 ]; then
		: >"$LOG_FILE" 2>/dev/null || LOG_FILE="/tmp/phpgt-installer.log"
		: >"$LOG_FILE" 2>/dev/null || true
	fi
}

show_verbose_hint() {
	if [ "$VERBOSE" -eq 0 ]; then
		warn "Re-run with --verbose for detailed output."
	fi
}

prompt_default() {
	prompt="$1"
	default_value="$2"

	if can_prompt; then
		printf '%s' "$prompt" > /dev/tty
		read answer < /dev/tty || answer=""
	else
		answer=""
	fi

	if [ -n "$answer" ]; then
		printf '%s' "$answer"
	else
		printf '%s' "$default_value"
	fi
}

infer_shell() {
	if [ -n "$SHELL_OVERRIDE" ]; then
		case "$SHELL_OVERRIDE" in
			bash|zsh|sh)
				printf '%s' "$SHELL_OVERRIDE"
				return
				;;
		esac
	fi

	parent_shell=""
	if have_command ps; then
		parent_shell="$(ps -p "$PPID" -o comm= 2>/dev/null | awk '{print $1}')"
		case "$parent_shell" in
			bash|zsh|sh)
				printf '%s' "$parent_shell"
				return
				;;
		esac

		ppid2="$(ps -p "$PPID" -o ppid= 2>/dev/null | awk '{print $1}')"
		if [ -n "$ppid2" ]; then
			parent_shell="$(ps -p "$ppid2" -o comm= 2>/dev/null | awk '{print $1}')"
			case "$parent_shell" in
				bash|zsh|sh)
					printf '%s' "$parent_shell"
					return
					;;
			esac
		fi
	fi

	if [ -n "$SHELL" ]; then
		case "$(basename "$SHELL")" in
			bash|zsh|sh)
				basename "$SHELL"
				return
				;;
		esac
	fi

	printf 'sh'
}

select_shell() {
	default_shell="$(infer_shell)"
	case "$default_shell" in
		bash|zsh|sh) ;;
		*) default_shell="sh" ;;
	esac

	choice="$(prompt_default "Which shell profile should I update? [bash/zsh/sh] (default: ${default_shell}): " "$default_shell")"
	case "$choice" in
		bash|zsh|sh) SELECTED_SHELL="$choice" ;;
		*) SELECTED_SHELL="$default_shell" ;;
	esac

	case "$SELECTED_SHELL" in
		bash) SHELL_RC_FILE="${HOME}/.bashrc" ;;
		zsh) SHELL_RC_FILE="${HOME}/.zshrc" ;;
		sh) SHELL_RC_FILE="${HOME}/.profile" ;;
	esac
}

ensure_line_in_file() {
	target_file="$1"
	line="$2"

	touch "$target_file"
	if grep -F "$line" "$target_file" >/dev/null 2>&1; then
		return 0
	fi

	printf '\n%s\n' "$line" >> "$target_file"
}

ensure_path_export() {
	target_dir="$1"
	label="$2"

	case ":$PATH:" in
		*":$target_dir:"*)
			return 0
			;;
	esac

	add_path_choice="$(prompt_default "Add ${label} to PATH in ${SHELL_RC_FILE}? [Y/n]: " "Y")"
	case "$add_path_choice" in
		n|N)
			say "Skipped PATH update. Add this manually:"
			say "export PATH=\"\$PATH:${target_dir}\""
			;;
		*)
			ensure_line_in_file "$SHELL_RC_FILE" "export PATH=\"\$PATH:${target_dir}\""
			say "Added PATH export to ${SHELL_RC_FILE}: ${target_dir}"
			;;
	esac
}

choose_writable_bin_dir() {
	if [ -d /usr/local/bin ] && [ -w /usr/local/bin ]; then
		printf '/usr/local/bin'
		return
	fi

	user_bin="${HOME}/.local/bin"
	if [ -z "$HOME" ]; then
		user_bin="/tmp/.local/bin"
	fi

	mkdir -p "$user_bin" >/dev/null 2>&1 || true
	printf '%s' "$user_bin"
}

install_gt_launcher_native() {
	launcher_dir="$(choose_writable_bin_dir)"
	launcher_path="${launcher_dir}/gt"
	gt_target="${COMPOSER_BIN_DIR}/gt"

	if [ ! -f "$gt_target" ]; then
		warn "Installed package did not expose gt at ${gt_target}."
		return 1
	fi

	if ! {
		printf '%s\n' '#!/usr/bin/env sh'
		printf 'exec "%s" "$@"\n' "$gt_target"
	} > "$launcher_path"; then
		warn "Could not create gt launcher at ${launcher_path}."
		return 1
	fi

	chmod +x "$launcher_path" 2>/dev/null || true
	GT_LAUNCHER_PATH="$launcher_path"
	return 0
}

install_gt_launcher_docker() {
	launcher_dir="$(choose_writable_bin_dir)"
	launcher_path="${launcher_dir}/gt"

	if ! {
		printf '%s\n' '#!/usr/bin/env sh'
		printf '%s\n' 'exec docker run --rm -it \'
		printf '%s\n' '  -v "$PWD:/app" \'
		printf '  -v "%s:/tmp/composer" \\\n' "$COMPOSER_HOME_DIR"
		printf '%s\n' '  -e COMPOSER_HOME=/tmp/composer \'
		printf '%s\n' '  -w /app \'
		printf '%s\n' '  php:8.4-cli \'
		printf '%s\n' '  php /tmp/composer/vendor/phpgt/gtcommand/bin/gt "$@"'
	} > "$launcher_path"; then
		warn "Could not create gt launcher at ${launcher_path}."
		return 1
	fi

	chmod +x "$launcher_path" 2>/dev/null || true
	GT_LAUNCHER_PATH="$launcher_path"
	return 0
}

php_is_supported() {
	if ! have_command php; then
		return 1
	fi

	version="$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;' 2>/dev/null)"
	major="$(printf '%s' "$version" | cut -d. -f1)"
	minor="$(printf '%s' "$version" | cut -d. -f2)"

	case "$major" in ''|*[!0-9]*) return 1 ;; esac
	case "$minor" in ''|*[!0-9]*) return 1 ;; esac

	if [ "$major" -gt "$MIN_PHP_MAJOR" ]; then
		return 0
	fi
	if [ "$major" -lt "$MIN_PHP_MAJOR" ]; then
		return 1
	fi
	[ "$minor" -ge "$MIN_PHP_MINOR" ]
}

php_has_zip_extension() {
	if ! have_command php; then
		return 1
	fi
	php -m 2>/dev/null | grep -iq '^zip$'
}

detect_package_manager() {
	if have_command apt-get; then
		printf 'apt-get'
	elif have_command dnf; then
		printf 'dnf'
	elif have_command yum; then
		printf 'yum'
	elif have_command apk; then
		printf 'apk'
	elif have_command pacman; then
		printf 'pacman'
	elif have_command zypper; then
		printf 'zypper'
	elif have_command brew; then
		printf 'brew'
	else
		printf 'unknown'
	fi
}

show_install_suggestions() {
	pm="$(detect_package_manager)"
	case "$pm" in
		apt-get)
			say "Native prerequisites example: apt-get update && apt-get install -y curl git unzip php-cli php-zip"
			say "Docker example: apt-get update && apt-get install -y docker.io"
			;;
		dnf)
			say "Native prerequisites example: dnf install -y curl git unzip php-cli php-zip"
			say "Docker example: dnf install -y docker"
			;;
		yum)
			say "Native prerequisites example: yum install -y curl git unzip php-cli php-zip"
			say "Docker example: yum install -y docker"
			;;
		apk)
			say "Native prerequisites example: apk add curl git unzip php84 php84-phar php84-zip"
			say "Docker example: apk add docker-cli"
			;;
		pacman)
			say "Native prerequisites example: pacman -S --needed curl git unzip php"
			say "Docker example: pacman -S --needed docker"
			;;
		zypper)
			say "Native prerequisites example: zypper install -y curl git unzip php8 php8-zip"
			say "Docker example: zypper install -y docker"
			;;
		brew)
			say "Native prerequisites example: brew install curl git unzip php"
			say "Docker example: install Docker Desktop"
			;;
		*)
			say "Install native prerequisites: PHP >= 8.4, curl, and unzip (or 7z) or git."
			say "Or install Docker."
			;;
	esac
}

show_missing_native_requirements() {
	say "To install natively, you need: PHP >= 8.4, curl, and unzip/7z (or PHP zip extension) or git."
	if [ "$NATIVE_PHP_OK" -eq 0 ]; then
		say "Missing: PHP >= 8.4"
	fi
	if [ "$NATIVE_CURL_OK" -eq 0 ]; then
		say "Missing: curl"
	fi
	if [ "$NATIVE_ARCHIVE_OK" -eq 0 ] && [ "$NATIVE_GIT_OK" -eq 0 ]; then
		say "Missing: unzip/7z (or PHP zip extension) and git"
	fi
}

preflight() {
	NATIVE_PHP_OK=0
	NATIVE_CURL_OK=0
	NATIVE_ARCHIVE_OK=0
	NATIVE_GIT_OK=0
	NATIVE_OK=0
	DOCKER_OK=0
	HAS_DOCKER=0

	if php_is_supported; then
		NATIVE_PHP_OK=1
	fi
	if have_command curl; then
		NATIVE_CURL_OK=1
	fi
	if php_has_zip_extension || have_command unzip || have_command 7z; then
		NATIVE_ARCHIVE_OK=1
	fi
	if have_command git; then
		NATIVE_GIT_OK=1
	fi
	if have_command docker; then
		DOCKER_OK=1
		HAS_DOCKER=1
	fi

	if [ "$NATIVE_PHP_OK" -eq 1 ] && [ "$NATIVE_CURL_OK" -eq 1 ] && { [ "$NATIVE_ARCHIVE_OK" -eq 1 ] || [ "$NATIVE_GIT_OK" -eq 1 ]; }; then
		NATIVE_OK=1
	fi
}

set_composer_paths() {
	if [ -n "$COMPOSER_CMD" ]; then
		COMPOSER_HOME_DIR="$(run_composer config --global home 2>/dev/null)"
		COMPOSER_BIN_DIR="$(run_composer global config bin-dir --absolute 2>/dev/null)"
	fi

	if [ -z "$COMPOSER_HOME_DIR" ]; then
		COMPOSER_HOME_DIR="${COMPOSER_HOME:-$DEFAULT_COMPOSER_HOME}"
	fi
	if [ -z "$COMPOSER_BIN_DIR" ]; then
		COMPOSER_BIN_DIR="${COMPOSER_HOME_DIR}/vendor/bin"
	fi
}

install_native_composer_if_missing() {
	if have_command composer; then
		COMPOSER_CMD="composer"
		COMPOSER_NEEDS_PHP=0
		COMPOSER_SHIM_DIR="$(dirname "$(command -v composer)")"
		return 0
	fi

	install_choice="$(prompt_default "Composer is not installed. Install it now? [Y/n]: " "Y")"
	case "$install_choice" in
		n|N)
			warn "Composer is required for native installation."
			return 1
			;;
	esac

	local_bin_dir="$(choose_writable_bin_dir)"
	phar_path="${local_bin_dir}/composer.phar"
	shim_path="${local_bin_dir}/composer"

	mkdir -p "$local_bin_dir" || return 1
	writable_probe="${local_bin_dir}/.gt-write-test.$$"
	if ! ( : > "$writable_probe" ) 2>/dev/null; then
		warn "Cannot write to ${local_bin_dir}."
		return 1
	fi
	rm -f "$writable_probe"

	if ! have_command curl; then
		warn "curl is required to install Composer natively."
		return 1
	fi

	if ! run_with_feedback "Downloading Composer..." curl -fsSL "https://getcomposer.org/composer-stable.phar" -o "$phar_path"; then
		warn "Unable to download Composer PHAR with curl."
		show_verbose_hint
		return 1
	fi

	if [ ! -s "$phar_path" ]; then
		warn "Composer PHAR was not created at ${phar_path}."
		return 1
	fi

	if ! {
		printf '%s\n' '#!/usr/bin/env sh'
		printf 'exec php "%s" "$@"\n' "$phar_path"
	} > "$shim_path"; then
		warn "Could not create Composer launcher at ${shim_path}."
		COMPOSER_CMD="$phar_path"
		COMPOSER_NEEDS_PHP=1
		COMPOSER_SHIM_DIR="$local_bin_dir"
		return 0
	fi

	chmod +x "$shim_path" 2>/dev/null || true
	COMPOSER_CMD="$shim_path"
	COMPOSER_NEEDS_PHP=0
	COMPOSER_SHIM_DIR="$local_bin_dir"
	return 0
}

choose_platform() {
	if [ "$NATIVE_OK" -eq 1 ] && [ "$DOCKER_OK" -eq 0 ]; then
		printf 'native'
		return
	fi
	if [ "$NATIVE_OK" -eq 0 ] && [ "$DOCKER_OK" -eq 1 ]; then
		printf 'docker'
		return
	fi

	choice="$(prompt_default "Native and Docker are available. Which platform should I use? [native/docker] (default: native): " "native")"
	case "$choice" in
		docker) printf 'docker' ;;
		*) printf 'native' ;;
	esac
}

install_native() {
	say "Using native PHP."

	if ! install_native_composer_if_missing; then
		if [ "$HAS_DOCKER" -eq 1 ]; then
			fallback_choice="$(prompt_default "Native Composer setup failed. Fall back to Docker installation instead? [Y/n]: " "Y")"
			case "$fallback_choice" in
				n|N) ;;
				*)
					install_docker
					return 0
					;;
			esac
		fi
		warn "Could not install Composer natively."
		warn "See ${DOCS_URL}"
		exit 1
	fi

	set_composer_paths
	if ! run_with_feedback "Installing ${PACKAGE_NAME} with Composer..." run_composer global require --no-interaction --no-progress "$PACKAGE_NAME"; then
		warn "Failed to install ${PACKAGE_NAME} with Composer."
		if [ "$NATIVE_ARCHIVE_OK" -eq 0 ] && [ "$NATIVE_GIT_OK" -eq 0 ]; then
			warn "Install unzip (or 7z / PHP zip extension) and git, then retry."
		fi
		show_verbose_hint
		warn "See ${DOCS_URL}"
		exit 1
	fi

	if ! have_command composer && [ -n "$COMPOSER_SHIM_DIR" ]; then
		ensure_path_export "$COMPOSER_SHIM_DIR" "Composer executable directory (${COMPOSER_SHIM_DIR})"
	fi

	if ! install_gt_launcher_native; then
		warn "Could not create gt launcher."
		warn "See ${DOCS_URL}"
		exit 1
	fi
	ensure_path_export "$(dirname "$GT_LAUNCHER_PATH")" "gt launcher directory ($(dirname "$GT_LAUNCHER_PATH"))"
}

install_docker() {
	say "Using Docker + Composer container."

	COMPOSER_HOME_DIR="${COMPOSER_HOME:-$DEFAULT_COMPOSER_HOME}"
	mkdir -p "$COMPOSER_HOME_DIR"

	if ! run_with_feedback "Installing ${PACKAGE_NAME} with Docker Composer..." docker run --rm -i \
		-v "${COMPOSER_HOME_DIR}:/tmp/composer" \
		-e COMPOSER_HOME=/tmp/composer \
		composer:2 \
		composer global require --no-interaction --no-progress "$PACKAGE_NAME"; then
		warn "Failed to install ${PACKAGE_NAME} using Docker."
		show_verbose_hint
		warn "See ${DOCS_URL}"
		exit 1
	fi

	if ! install_gt_launcher_docker; then
		warn "Could not create gt launcher."
		warn "See ${DOCS_URL}"
		exit 1
	fi
	ensure_path_export "$(dirname "$GT_LAUNCHER_PATH")" "gt launcher directory ($(dirname "$GT_LAUNCHER_PATH"))"
}

main() {
	parse_args "$@"
	init_log
	check_existing_gt
	preflight

	if [ "$NATIVE_OK" -eq 0 ] && [ "$DOCKER_OK" -eq 0 ]; then
		show_missing_native_requirements
		say "Alternatively, you can install Docker."
		show_install_suggestions
		warn "See ${ENV_DOCS_URL}"
		exit 1
	fi

	if [ "$NATIVE_OK" -eq 0 ] && [ "$DOCKER_OK" -eq 1 ]; then
		show_missing_native_requirements
		say "Docker is available, so this installer will use Docker."
		say "See ${ENV_DOCS_URL}"
	fi

	platform="$(choose_platform)"
	select_shell

	if [ "$platform" = "docker" ]; then
		install_docker
	else
		install_native
	fi

	if [ -n "$GT_LAUNCHER_PATH" ]; then
		say "gt launcher installed at: ${GT_LAUNCHER_PATH}"
	fi
	say "Installation complete."
	say "Close this terminal and open a new one to refresh your PATH."
	say "Then run: gt --help"
}

main "$@"
