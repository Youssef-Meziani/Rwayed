docker compose exec www bash -c "composer self-update && composer install && exit"
docker compose exec www_admin bash -c "composer self-update && composer install && exit"
docker compose exec www_api bash -c "composer self-update && composer install && exit"
pause