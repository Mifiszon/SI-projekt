# 🍳 Recipe Forum — Symfony + Docker

A recipe-sharing web forum built with **PHP Symfony 8.3** and containerised with **Docker**. Users can add, rate, search, and manage recipes with full role-based access control.

---

## 🧩 Features

- **User auth** — registration, login, and role-based access control (user / admin)
- **Recipe management** — full CRUD: add, edit, delete, rate recipes with image uploads
- **Recipe search engine** — search by name, ingredient, or category
- **Admin panel** — manage users, recipes, and reported content
- **Email testing** — integrated Maildev for local email simulation
- **Responsive UI** — Bootstrap 5

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3, Symfony LTS |
| Templating | Twig |
| Database | MySQL 8.3 |
| Server | Apache 2.4 (Debian) |
| Containerisation | Docker, Docker Compose |
| Dev Tools | Xdebug, Maildev, Composer, NodeJS LTS |

---

## ⚙️ Local Setup

**1. Clone and build containers**

```bash
git clone https://github.com/Mifiszon/Cookbook-Forum.git
cd Cookbook-Forum
bash build-env.sh          # Windows: build-env.ps1
```

**2. Enter the PHP container and install Symfony**

```bash
docker-compose exec php bash
cd app && rm .gitkeep
symfony new ../app --version=lts --webapp
chown -R dev.dev *
```

**3. Configure database in `.env`**

```env
DATABASE_URL=mysql://symfony:symfony@mysql:3306/symfony?serverVersion=8.3
```

**4. Run migrations**

```bash
php bin/console doctrine:migrations:migrate
```

---

## 🌐 URLs & Ports

| Service | URL |
|---|---|
| App | http://localhost:8000 |
| App (hosts alias) | http://symfony.local:8000 |
| Maildev | http://localhost:8001 |
| MySQL (external) | localhost:3307 |
| Xdebug | port 9000 |

---

## 🐳 Docker Commands

```bash
docker-compose up -d          # start all containers
docker-compose down           # stop containers
docker-compose exec php bash  # enter PHP container
docker-compose exec mysql bash # enter MySQL container
```

**Troubleshooting:** If you hit `ERROR: for apache 'ContainerConfig'` after `docker-compose up -d`:

```bash
docker compose up -d --force-recreate
```

---

## 🔭 Future Development

- Expanded search (filters by rating, prep time, dietary tags)
- REST API endpoint for recipe data export
- Pagination and infinite scroll for recipe feed
- Enhanced admin analytics (most viewed, top-rated recipes)

---

## 👨‍💻 Author

**Michał Ogiba** — Jagiellonian University, 2024  
[linkedin.com/in/michalogiba](https://linkedin.com/in/michalogiba) · [github.com/Mifiszon](https://github.com/Mifiszon)
