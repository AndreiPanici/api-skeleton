## Api-skeleton
Symfony 4 api-skeleton.

A simple api skeleton for basic REST API using symfony flex.

Used packages:
- jwt token
- uuid
- nelmio api doc

## Setup
 
 Generate ssh keys for jwt:
 ```
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`
```

## Install
`composer install`
`bin/console doctrine:migrations:generate`

To run the server: `symfony server:start`

## Test 

Run all tests: `php bin/phpunit`