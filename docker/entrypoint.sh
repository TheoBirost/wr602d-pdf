#!/bin/sh
set -e

# Démarrer Supervisor, qui gère Nginx et PHP-FPM
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/app.conf
