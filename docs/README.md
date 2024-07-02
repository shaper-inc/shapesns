# shapersns

## .env

exammple:

```ini
MYSQL_ROOT_PASSWORD=password
MYSQL_DATABASE=db_local
MYSQL_USER=wp_user
MYSQL_PASSWORD=password
```

## duccker run

```bash
docker compose up -d
```

```bash
docker compose down --rmi all --volumes --remove-orphans
```

## Timber/Twig

- Wordpress プラグインサイトからインストールするとバージョンが古いので動かない

https://timber.github.io/docs/v2/:

```bash
docker compose run wordpress bash
cd wp-content/plugins/shapesns/
composer require timber/timber
```
