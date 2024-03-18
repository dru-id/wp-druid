#!/bin/bash

#cd wp-wog-theme; git pull
#cd ..
docker cp wp-wog-theme/. wog-wp:/var/www/html/wp-content/themes/wog/
docker-compose exec wp chown -R www-data:www-data /var/www/html/wp-content/themes/wog

