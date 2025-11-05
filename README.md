# 🚀 XmlXsd Project — Quick Setup Guide

This project runs **Laravel** inside **Docker** with **Nginx**, **PHP-FPM**, and **MySQL**.

---

## 🐳 Step 1 — Build and Start Containers

Run the following command to build and start all containers:

```bash
docker compose up -d --build
```

This will:
- Build the PHP-FPM container
- Start Nginx and MySQL
- Automatically install Composer dependencies
- Generate the app key
- Run database migrations

You can check logs to confirm everything is ready:
```bash
docker compose logs -f app
```

---

## ⚙️ Step 2 — Start the Laravel Queue Worker (Required)

Once containers are up, open a shell inside the app container:

```bash
docker compose exec app bash
```

Then start the queue worker manually:

```bash
php artisan queue:work
```

> 🧠 **Note:** The queue worker should stay running to process background jobs.

---

## 🧱 Step 3 — Frontend Build (on Host Machine)

On your **host machine**, install and build frontend assets:

```bash
npm install && npm run build
```

This compiles all frontend resources for production.

---

## 🌍 Step 4 — Access the Application

After everything is running, open your browser and visit:

👉 [http://localhost:8080](http://localhost:8080)

You should see your Laravel app running inside Docker 🎉

---

## 🧰 Useful Commands

| Command | Description |
|----------|-------------|
| `docker compose ps` | List running containers |
| `docker compose exec app bash` | Enter the app container |
| `docker compose logs -f app` | View Laravel logs |
| `docker compose down` | Stop all containers |
| `php artisan queue:work` | Run Laravel queue manually |

---

## 🧩 Environment Configuration (`.env`)

Ensure your `.env` file includes the correct database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=xmlxsd
DB_USERNAME=xmlxsd_user
DB_PASSWORD=xmlxsd_pass
```

> 💡 The host name must be `mysql`, since it points to the database container, **not** your local machine.

---

✅ **Done!**
Your Laravel + Docker + Nginx + MySQL setup is ready to go.
