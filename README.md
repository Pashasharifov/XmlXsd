⚙️ Step 1 — Prepare Environment File (.env)

Before starting containers, make sure your .env file is properly configured.

Example configuration:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=xmlxsd
DB_USERNAME=xmlxsd_user
DB_PASSWORD=xmlxsd_pass


🐳 Step 2 — Build and Start Containers

Once .env is ready, build and start all containers with:

docker compose up -d --build

If queue container down , you can restart -- docker compose restart queue