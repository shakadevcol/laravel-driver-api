## Laravel Driver API

[Laravel Driver API](http://3.95.116.225:82/) is an API for small ride-hailing service, that use the payment plataform.

## A brief explanation

1. A rider logs in with his credencials using the endpoint "api/login". (this endpoint returns a token)

```bash
email: rider@gmail.com
password: 123456
```

**Note:** The next steps (endpoints) needs that the user (driver or rider) sends the token that was obtained in the login as a bearer token.

2. The rider adds a payment method using the enpoint **"api/payments/payment-method"**.

3. The rider request a new ride using the enpoint **"/api/rides"**

4. A driver finish a ride using the endpoint **"/api/rides/{ride_id}"**. Sending the final location (latitude, longitude).

These are the enpoints availables to complete the flow.

| Method | Endpoint                    | Funcionality                                |
| ------ | --------------------------- | ------------------------------------------- |
| POST   | api/login                   | Login for drivers and riders                |
| POST   | api/payments/payment-method | Allows a rider user to add a payment method |
| POST   | /api/rides                  | Allows a rider user request a ride          |
| PUT    | /api/rides/{ride_id}        | Allows a driver user finish a ride          |

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

5. In the .env file set your wompi keys using WOMPI_PUBLIC_KEY and WOMPI_PRIVATE_KEY variables

6. Run the database migration and seeders

```bash
php artisan migrate --seed
```

7. Run tests

```bash
php artisan test
```

8. (Optional) In the .env file declare the domain for your frontend project, using the "SANCTUM_STATEFUL_DOMAINS" variable. In case you want to connect your frontend application.

9. Open [http://localhost:9000](http://localhost:9000) with your browser. You should see a welcome page.

10. Test de API making requests to the enpoints available.

## Live Demo

Open [http://3.95.116.225:82/](http://3.95.116.225:82/) with your browser to see a live preview.
