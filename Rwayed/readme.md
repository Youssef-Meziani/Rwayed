# Run Quality Analysis tool

## Rules

Inside the `www` container, create a new file `.php-cs-fixer.php` from the `.php-cs-fixer.dist.php`

> docker compose exec www bash
>
> cp .php-cs-fixer.dist.php .php-cs-fixer.php

## Run the analysis

To run the analysis, inside the container `www`, run the following command

> ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix src/Controller/ --dry-run --diff

This command will run analysis for the entire `src/Controller` without actually fixing the code quality issues. To
automatically fix those issues, remove the `--dry-run` option