
# Project Title

A brief description of what this project does and who it's for


## Deployment

To deploy this project run

```bash
  1.composer install
  2.npm install
  3.cp .env.example .env
  4.php artisan key:generate
```
after that update the env file. then,

```bash
  6.php artisan migrate
  8.php artisan db:seed
```

clear cache
```bash
  8.php artisan cache:clear
```

unit test
```bash
10. php artisan test
```


