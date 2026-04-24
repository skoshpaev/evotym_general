# evotym_general

Это корневой проект-оркестратор для тестового задания.

Тут одной командой поднимаются:
- `product`
- `order`
- `rabbitmq`

Если папок `product/` или `order/` нет, bootstrap сам их клонирует.

Важно:
- всё, что тут связано с `.env`, токенами, `guest/guest`, паролями MySQL и прочими встроенными значениями, закоммичено только ради быстрого локального теста
- для реального проекта так делать нельзя

## Что нужно перед стартом

Нужен только Docker с Compose.

Ещё важно:
- на первом старте нужен доступ в интернет
- должны быть свободны порты `8081`, `8082`, `5672`, `15672`

Самый важный момент:
- запускать нужно именно из директории cloned `evotym_general`
- то есть сначала зайти в папку репозитория, и только потом запускать compose
- не надо запускать это из соседней папки

Тут есть завязка на текущую директорию запуска, поэтому запуск "не из корня проекта" может сломать bootstrap и volume mount-ы

Я запускал так:

```bash
git clone git@github.com:skoshpaev/evotym_general.git
cd evotym_general
docker compose up -d --build
```

Первый старт может быть небыстрым.
Это нормально, потому что в этот момент:
- клонируются `product` и `order`
- собираются Docker image
- ставятся зависимости
- прогоняются миграции

Проверить, что всё поднялось:

```bash
docker compose ps
```

Нормальная картина такая:
- `bootstrap-projects` завершился
- `product-app`, `product-consumer`, `product-publisher`, `product-nginx`, `product-mysql` работают
- `order-app`, `order-consumer`, `order-publisher`, `order-nginx`, `order-mysql` работают
- `rabbitmq` работает

То есть быстрый чек-лист после старта такой:
- сначала дождаться, пока `bootstrap-projects` завершится
- потом проверить `docker compose ps`
- и только потом делать `curl` / HTTP-проверки

## Куда стучаться

HTTP:
- product health: `http://127.0.0.1:8081/health.php`
- product api: `http://127.0.0.1:8081/products`
- order health: `http://127.0.0.1:8082/health.php`
- order api: `http://127.0.0.1:8082/orders`

Важно:
- в list endpoints пагинацию я не делал, просто чтобы не тратить на это время в рамках тестового задания

RabbitMQ:
- AMQP: `127.0.0.1:5672`
- UI: `http://127.0.0.1:15672`
- login: `guest`
- password: `guest`

## Токен

У `product` включён bearer token.

Для локальной проверки:

```text
Authorization: Bearer product-dev-token
```

Это тоже сделано только ради быстрого теста.

## Быстрая проверка после старта

Health:

```bash
curl -fsS http://127.0.0.1:8081/health.php
curl -fsS http://127.0.0.1:8082/health.php
```

Список продуктов:

```bash
curl -sS \
  -H 'Authorization: Bearer product-dev-token' \
  http://127.0.0.1:8081/products
```

Тут сейчас без пагинации.

Создать продукт:

```bash
curl -sS \
  -X POST http://127.0.0.1:8081/products \
  -H 'Authorization: Bearer product-dev-token' \
  -H 'Content-Type: application/json' \
  -d '{
    "name": "Coffee Mug",
    "price": 12.99,
    "quantity": 10
  }'
```

Список заказов:

```bash
curl -sS http://127.0.0.1:8082/orders
```

Тут тоже сейчас без пагинации.

Создать заказ:

```bash
curl -sS \
  -X POST http://127.0.0.1:8082/orders \
  -H 'Content-Type: application/json' \
  -d '{
    "productId": "PUT_PRODUCT_UUID_HERE",
    "customerName": "John Doe",
    "quantityOrdered": 2
  }'
```

После создания заказа статус сначала создаётся как `Processing`, потом подтверждается через RabbitMQ.
Обычно это быстро, но логика тут event-driven, не в одной синхронной транзакции между сервисами.

## Тесты

Тесты гонял так:

```bash
docker exec evotym-product-app-1 vendor/bin/phpunit -c phpunit.dist.xml
docker exec evotym-order-app-1 vendor/bin/phpunit -c phpunit.dist.xml
```

## Что тут внутри по смыслу

- `product` хранит товары
- `order` хранит заказы и свою локальную копию товаров
- между собой они общаются через RabbitMQ
- для исходящих сообщений используется outbox
- для входящих сообщений есть inbox
- общие штуки вынесены в отдельный shared bundle

Если нужно смотреть каждый сервис отдельно, у них есть свои README:
- [product/README.md](product/README.md)
- [order/README.md](order/README.md)
