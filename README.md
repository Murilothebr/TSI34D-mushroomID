## 🍄 Mushroom Id

Bem-vindo ao Mushroom Id! Este projeto tem como objetivo auxiliar no aprendizado da identificação de cogumelos através de um sistema interativo de quiz.

## 📌 Sobre o Projeto
O sistema permite que administradores cadastrem novas espécies de cogumelos com imagens e descrições. Os usuários podem visualizar esses cogumelos em forma de cards e tentar adivinhar a espécie correta com base nas informações fornecidas.

## 🎯 Objetivo
O projeto busca tornar o aprendizado sobre cogumelos mais acessível e dinâmico, promovendo conscientização e evitando erros na identificação, que podem ter consequências graves.

### Dependências

- Docker
- Docker Compose

### To run

#### Clone Repository

```
$ git clone git@github.com:SI-DABE/problem-track.git
$ cd problem-track
```

#### Define the env variables

```
$ cp .env.example .env
```

#### Install the dependencies

```
$ ./run composer install
```

#### Up the containers

```
$ docker compose up -d
```

ou

```
$ ./run up -d
```

#### Create database and tables

```
$ ./run db:reset
```

#### Populate database

```
$ ./run db:populate
```

### Fixed uploads folder permission

```
sudo chown www-data:www-data public/assets/uploads
```

#### Run the tests

```
$ docker compose run --rm php ./vendor/bin/phpunit tests --color
```

ou

```
$ ./run test
```

#### Run the linters

[PHPCS](https://github.com/PHPCSStandards/PHP_CodeSniffer/)

```
$ ./run phpcs
```

[PHPStan](https://phpstan.org/)

```
$ ./run phpstan
```

Access [localhost](http://localhost)

### Teste de API

#### Rota não autenticada

```shell
curl -H "Accept: application/json" localhost/problems
```

#### Rota autenticada

Neste caso precisa alterar o valor do PHPSESSID de acordo com a o id da sua sessão.

```shell
curl -H "Accept: application/json" -b "PHPSESSID=5f55f364a48d87fb7ef9f18425a8ae88" localhost/problems
```
