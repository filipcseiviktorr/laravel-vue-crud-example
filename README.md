# Laravel-Vue CRUD Example

## Requirements
- Docker and Docker Compose installed on your machine.

## Setup Instructions

1. Copy the example environment file to create your own configuration:
   ```sh
   cp .env.example .env
   ```

2. Start the containers:
   ```sh
   docker-compose up -d
   ```

3. Install PHP dependencies inside the container:
   ```sh
   docker exec -it laravel_vue_app composer install
   ```

4. Generate a new application key:
   ```sh
   docker exec -it laravel_vue_app php artisan key:generate
   ```

5. Run the database migrations to set up the tables:
   ```sh
   docker exec -it laravel_vue_app php artisan migrate
   ```

6. Seed the database with initial data:
   ```sh
   docker exec -it laravel_vue_app php artisan db:seed
   ```

7. Create a symlink to the storage directory:
   ```sh
   docker exec -it laravel_vue_app php artisan storage:link
   ```

8. Install JavaScript dependencies inside the container:
   ```sh
   npm install
   ```

9. Compile the assets for development:
   ```sh
   npm run dev
   ```

10. Access the application:
    - [http://localhost](http://localhost)

11. Access the database:
    - phpMyAdmin: [http://localhost:8080](http://localhost:8080)

## Useful Commands

- Stop the containers:
  ```sh
  docker-compose down
  ```

- Restart the containers:
  ```sh
  docker-compose restart
  ```

- Access the application container:
  ```sh
  docker exec -it laravel_vue_app bash
  ```