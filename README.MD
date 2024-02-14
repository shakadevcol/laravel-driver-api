This is the [Laravel Driver API]() project.

## Getting Started

First, you need to have ["Docker"](https://www.docker.com/) installed in your machine
then:

1. Download this repository.
2. Initialize the container and install the composer packages:

```bash
make up
make serv
composer install
```

3. Set the correct permissions and group for these folders:

```bash
chgrp -R www-data storage
chmod -R 770 storage

chgrp -R www-data bootstrap/cache
chmod -R 770 bootstrap/cache
```

3. Go to "app" folder and duplicate the ".env.example" with the name ".env"

4. Generate the application key

```bash
php artisan key:generate
```

5. In the .env file declare the domain for your frontend project, using the "SANCTUM_STATEFUL_DOMAINS" variable.
6. Run the database migration and seeders

```bash
php artisan migrate --seed
```

7. Open [http://localhost:9000](http://localhost:9000) with your browser to see the result.

8. Test making requests to the enpoints delcared in the file "routes/api.php"

## Live Demo

Open []() with your browser to see a live preview.
