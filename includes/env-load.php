<?php
// Load project-level environment values once. Existing server environment
// variables always win over values from the local .env file.
if (!defined('CBD_ENV_LOADED')) {
    define('CBD_ENV_LOADED', true);
    $_cbd_env_file = dirname(__DIR__) . '/.env';
    if (is_readable($_cbd_env_file)) {
        foreach (file($_cbd_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $_cbd_env_line) {
            $_cbd_env_line = trim($_cbd_env_line);
            if ($_cbd_env_line === '' || $_cbd_env_line[0] === '#' || strpos($_cbd_env_line, '=') === false) continue;
            [$_cbd_env_key, $_cbd_env_value] = explode('=', $_cbd_env_line, 2);
            $_cbd_env_key = trim($_cbd_env_key);
            $_cbd_env_value = trim($_cbd_env_value);
            $_cbd_env_length = strlen($_cbd_env_value);
            if ($_cbd_env_length >= 2
                && $_cbd_env_value[0] === $_cbd_env_value[$_cbd_env_length - 1]
                && in_array($_cbd_env_value[0], ['"', "'"], true)) {
                $_cbd_env_value = substr($_cbd_env_value, 1, -1);
            }
            if ($_cbd_env_key !== '' && getenv($_cbd_env_key) === false && !array_key_exists($_cbd_env_key, $_ENV)) {
                $_ENV[$_cbd_env_key] = $_cbd_env_value;
                putenv($_cbd_env_key . '=' . $_cbd_env_value);
            }
        }
    }
    unset($_cbd_env_file, $_cbd_env_line, $_cbd_env_key, $_cbd_env_value, $_cbd_env_length);
}
