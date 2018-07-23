#!/usr/bin/env bash

environment=$1
if [ -z "$environment" ]
  then
    environment="dev"
fi

composer install;

bin/console doctrine:database:create --if-not-exists --env=${environment};
bin/console doctrine:migrations:migrate --no-interaction --env=${environment};

if [ "$environment" != "prod" ]
  then
bin/console doctrine:fixtures:load --no-interaction --env=${environment};
fi

HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/log config/jwt/
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/log config/jwt/

cp phpunit.xml.dist phpunit.xml