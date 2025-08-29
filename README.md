1) PHP 8.2+, Composer, Node
2) Copy env and set SQLite:
3) `touch database/database.sqlite`
4) `composer install && php artisan key:generate`
5) `php artisan migrate --seed`
6) `npm install && npm run build`
7) `php artisan serve` → open http://127.0.0.1:8000

## Features
- Projects: list/show, edit/delete
- Issues: list with filters (status/priority/tag)
- Tags: list/create
- Comments & tag attach/detach via AJAX on issue page (WIP)
