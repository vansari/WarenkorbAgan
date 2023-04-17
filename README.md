![workflow](https://github.com/vansari/WarenkorbAgan/actions/workflows/php.yml/badge.svg)

## Coding Challenge

### prepare workspace

1. checkout this repo
2. symfony console doctrine:database:create
3. symfony console doctrine:migrations:migrate
4. symfony console doctrine:fixtures:load

### running all tests

```shell
composer test
```

### running codecept tests (api/functional/unit)
```shell
composer codecept
```
### running static tests
```shell
composer static
```

### API Resources

### Shopping Carts:
- you can create a new cart via Post
- you can add a new Item to existing cart
- you can delete an item from cart
- you can add an existing Item to a cart

[GET, POST]`/carts` all carts, create cart
#### GET Result:
```json
[
    {
        "items": array,
        "createdAt": DateTime,
        "updatedAt": DateTime,
        "total:" float
    }
]
```
#### POST payload
```json
{
    "items": [
        {
            "product": 1,
            "quantity": 1
        }
    ]
}
```

[GET]`/carts/{id}` one special cart
```json
{
    "items": array,
    "createdAt": DateTime,
    "updatedAt": DateTime,
    "total:" float
}
```
[GET] `/carts/{id}/items` all Items of special cart
```json
[
    {
        "product": 1,
        "quantity": 1
    }
]
```
[PUT] `/carts/{id}/items` add Item to special cart
```json
{
    "product": 1,
    "quantity": 1
}
```
[PATCH] `/carts/{id}/items` add Item to special cart (no payload required)

[DELETE] `/carts/{id}/items/{itemId}` delete one item from cart

### Shopping cart items
- you receive a json payload with product, quantity and total
- if you add or subtract an product item the total will be recalculated

[GET, POST] `/items` get list of items or create one

#### GET Result

```json
[
  {
    "product": {
      "id": 1,
      "name": "Shoes",
      "price": 99.99
    },
    "quantity": 1,
    "total": 99.99
  }
]
```

#### POST Payload
```json
{
    "product": 1,
    "quantity": 1
}
```

[GET] `/items/{id}` get one item by id
```json
{
    "product": {
      "id": 1,
      "name": "Shoes",
      "price": 99.99
    },
    "quantity": 2,
    "total": 199.98
}
```

[DELETE] `/items/{id}` delete one item by id

[GET] `/items/{id}/products` get Product of Item
```json
{
    "id": int,
    "name": string,
    "price": float
}
```

[PATCH] `/items/{id}/subtract` remove one product element from item

[PATCH] `/items/{id}/add` add one more Product element to item

### Products

You can request the Products simple via GET:

[GET] `/products` Get all products
[GET] `/products/{id}` Get one product

For development, you can also add or update products
[POST] `/products`
```json
{
  "name": string,
  "price": float
}
```
[PUT] `/products/{id}`
```json
{
  "name": string
}
```
OR
```json
{
  "price": float
}
```
OR
```json
{
  "name": string,
  "price": float
}
```
