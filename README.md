# Article Aggregator

This project is a Symfony-based application designed to aggregate articles from various sources such as databases, RSS feeds, and external APIs. It also includes a REST API for accessing stored articles.

## Prerequisites

- PHP 8.*
- Composer
- Symfony CLI
- A database (e.g., MySQL, PostgreSQL)

## Installation

### Step 1: Install dependencies

composer install
Step 2: Create the database

php bin/console doctrine:database:create

Step 3: Update the database schema

php bin/console doctrine:schema:update --force

Step 4: Load initial data (to create a user)

php bin/console doctrine:fixtures:load

Usage
Authentication
Go to the /api/login_check endpoint with the following payload to generate a JWT token:

json
Copier le code
{
    "email": "elwaghef@gmail.com",
    "password": "password"
}
You will receive a JWT token in the response. Use this token to access protected API URLs.

API Endpoints
Public Endpoints
GET /api/articles: Retrieve all articles.
Protected Endpoints
POST /api/articles: Create a new article (requires JWT token).
Accessing Protected Endpoints
To access protected endpoints, include the JWT token in the Authorization header as follows:


Authorization: Bearer YOUR_JWT_TOKEN
Replace YOUR_JWT_TOKEN with the token you received from the /api/login_check endpoint.

Example Usage with Postman
Get JWT Token

URL: http://127.0.0.1:8000/api/login_check
Method: POST
Headers: Content-Type: application/json
Body:
json
Copier le code
{
    "email": "elwaghef@gmail.com",
    "password": "password"
}
Access Protected Endpoint

URL: http://127.0.0.1:8000/api/articles
Method: POST
Headers:
Content-Type: application/json
Authorization: Bearer YOUR_JWT_TOKEN
Body:

{
    "sourceName": "Le Monde",
    "name": "Nouvel Article",
    "content": "Ceci est le contenu du nouvel article."
}
