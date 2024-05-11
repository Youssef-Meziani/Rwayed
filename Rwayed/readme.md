# QA : 

## Cs fixer 

to use CS FIXER, make a copy of the file `.php-cs-fixer.dist.php` to `.php-cs-fixer.php`

> cp .php-cs-fixer.dist.php .php-cs-fixer.php

next run the following command inside the container to fix styles : 

> ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix XXX

where XXX could be a file or a directory to analyze and fix. example: `./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix src/Controller/`.

if you want to analyze without fixing add `--dry-run` option. example 

> ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --diff src/Controller/SecurityController.php --dry-run

the `--diff` is useful to show the changes.
