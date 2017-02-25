#!/usr/bin/env bash

function empty_line {
    echo "";
}

#ARG__NO_CACHE_CLEAR="--no-cache-clear";

if [[ "$@" = *--help* ]]; then
    echo "Usage:";
    echo "  "$(basename $0)" [arguments]";

    empty_line;

    echo "Options:";
    echo "  --help                     Display this help message";
#    echo "  ${ARG__NO_CACHE_CLEAR}           Skip cache clear";

    exit 0;
fi;

empty_line;

EXE_PHP=$(which php);
EXE_COMPOSER=$(which composer.phar);

BOLD_ON=$(tput -Txterm bold);
BOLD_OFF=$(tput -Txterm sgr0);

# Updating dependencies
echo "${BOLD_ON}>> Syncing dependencies${BOLD_OFF}";
empty_line;
${EXE_PHP} -d disable_functions= ${EXE_COMPOSER} install --no-dev;
empty_line;
empty_line;

exit 0;
