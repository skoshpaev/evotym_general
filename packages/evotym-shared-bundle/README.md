# Evotym Shared Bundle

Reusable Symfony bundle for the Evotym services.

Included building blocks:

- shared product DTOs for HTTP responses and messaging
- reusable RabbitMQ topology configuration for product events
- a Doctrine mapped superclass for product entities

Planned usage:

- `product` service: product DTOs, RabbitMQ topology, mapped superclass
- `order` service: common product DTOs and mapped superclass for the local product projection
