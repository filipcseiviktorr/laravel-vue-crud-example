1. Ensure you have the following versions installed:
    - Laravel: ^11.31
    - Node.js: ^18.0.0
    - PHP: ^8.2

2. Copy the example environment file to create your own configuration:
   ```sh
   cp .env.example .env
   ```

3. Install the necessary PHP dependencies using Composer:
   ```sh
   composer install
   ```

4. Generate a new application key:
   ```sh
   php artisan key:generate
   ```

5. Create a new database in MySQL named 'crud'.

6. Run the database migrations to set up the tables:
   ```sh
   php artisan migrate
   ```

7. Seed the database with initial data:
   ```sh
   php artisan db:seed
   ```

8. Create a symlink to the storage directory:
   ```sh
   php artisan storage:link
   ```

9. Install the necessary JavaScript dependencies using npm:
   ```sh
   npm install
   ```

10. Compile the assets for development:
    ```sh
    npm run dev
    ```

11. Start the development server:
    ```sh
    php artisan serve
    ```
