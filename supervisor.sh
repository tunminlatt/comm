#!/bin/sh
set -e
crond
supervisord -c /etc/supervisord.conf &
