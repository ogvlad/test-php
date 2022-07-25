# What it is?

A sample PHP application powered by API Platform & Symfony

## Launching

1. Run database
2. Backend (REST API, Docs)
3. Frontend (Website, Admin)

## Run database

```
docker-compose build --pull --no-cache
docker-compose up -d
```

## Backend

Open new terminal and switch to `api` folder:

```
$ cd api
```

### Configure

Edit `.env` file - update connection string to what you want.

### Install dependencies

```
$ composer install
```

### Execute DB migrations

This will create all necessary tables in DB:

```
$ php bin/console doctrine:migrations:migrate
```

### Run parser

This will fetch all books from remote JSON file and populate DB

```
php bin/console app:parse
```

### Start Symfony server

> This is important that you run Symfony server, rather than PHP built-in. The lattest doesn't produce proper JSON-LD file thus frontend doesn't work.

```
symfony serve
```

## Frontend

Open terminal and switch to `pwa` folder:

```
$ cd pwa
```

### Configure

Edit `pwa\pages\admin\index.tsx`. Set this to point to your backend with port:
```
    return <HydraAdmin entrypoint="http://localhost:8000" />;
```

### Install dependencies

```
yarn install
```

### Run

```
yarn dev
```

