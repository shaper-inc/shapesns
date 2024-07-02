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

- https://ja.wordpress.org/plugins/timber-library/

```bash
docker compose run wordpress wp plugin install timber-library --allow-root
```
