#!/bin/sh
set -e

php spark migrate --all || true

exec apache2-foreground
