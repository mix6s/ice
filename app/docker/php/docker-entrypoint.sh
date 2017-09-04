#!/bin/bash

setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX /var/www/ice/var
setfacl -dR -m u:www-data:rwX -m u:`whoami`:rwX /var/www/ice/var

exec "$@"