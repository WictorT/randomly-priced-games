#!/usr/bin/env bash

composer install;

bin/console doctrine:migrations:migrate --no-interaction;
bin/console doctrine:fixtures:load --no-interaction;

HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/log config/jwt/
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/log config/jwt/