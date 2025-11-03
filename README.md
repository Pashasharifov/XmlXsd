## 🚀 Quick Setup — XmlXsd Project

### Environment Configuration (`.env`)
Make sure your `.env` file includes the correct database settings:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=xmlxsd
DB_USERNAME=xmlxsd_user
DB_PASSWORD=xmlxsd_pass

# 1. Build and start containers
docker compose up -d --build

# 2. Access the application container
docker compose exec app bash

# 3. Inside the container, install dependencies
composer install

# 4. Generate the Laravel app key
php artisan key:generate

# 5. Run database migrations
php artisan migrate

After setup, open your browser and visit:

👉 http://localhost:8080
