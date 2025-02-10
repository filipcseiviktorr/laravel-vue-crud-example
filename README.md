Here is the formatted version of the instructions:

1. Copy the example environment file to create your own configuration:
   ```sh
   cp .env.example .env
   ```

2. Install the necessary PHP dependencies using Composer:
   ```sh
   composer install
   ```

3. Generate a new application key:
   ```sh
   php artisan key:generate
   ```

4. Create a new database in MySQL named 'crud'.

5. Run the database migrations to set up the tables:
   ```sh
   php artisan migrate
   ```

6. Seed the database with initial data:
   ```sh
   php artisan db:seed
   ```

7. Create a symlink to the storage directory:
   ```sh
   php artisan storage:link
   ```

8. Start the development server:
   ```sh
   php artisan serve
   ```

9. Install the necessary JavaScript dependencies using npm:
   ```sh
   npm install
   ```

10. Compile the assets for development:
    ```sh
    npm run dev
    ```
