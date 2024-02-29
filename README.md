## Get Started

This guide will walk you through the steps needed to get this project up and running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:

- Docker
- Docker Compose

### Building the Docker Environment

Build and start the containers:

```
docker-compose up -d --build
```

### Installing Dependencies

```
docker-compose exec app sh
composer install
```

### Database Setup

Set up the database:

```
bin/cake migrations migrate
bin/cake migrations seed
```

### Accessing the Application

The application should now be accessible at http://localhost:34251

## How to check

### Authentication (Recommending to use Postman tool)

`POST http://localhost:34251/login`
![login](https://github.com/irfanmominmt/coding-test-php/assets/112695126/ad0c9875-08e1-43fa-bbb5-42588d71533a)

- Make sure to pass below JSON object inside raw type.

```
  {
    "email": "demo1@mailexample.com",
    "password": "password"
  }
```

- Unauthorized User message
![login_failed](https://github.com/irfanmominmt/coding-test-php/assets/112695126/6a2782a5-b281-42fb-9ce2-bb4fc012b9d5)

- Logout
`GET http://localhost:34251/users/logout`
![logout](https://github.com/irfanmominmt/coding-test-php/assets/112695126/4144331e-0179-4fed-80a7-fb34a243ceca)

### Article Management
- Create an Article
```
Only Logged in user can create an article
```
`POST http://localhost:34251/articles/add`
```
{
  "title": "Article title",
  "body": "Article body"
}
```
![Createarticle](https://github.com/irfanmominmt/coding-test-php/assets/112695126/45503a52-a456-4a7e-9a7a-4a6528707bfb)

- Print All the Articles array
```
- Guest users & Logged in users both can see the articles list.
- Shown Total Liked count
```
`GET http://localhost:34251/articles.json`
![list_articles](https://github.com/irfanmominmt/coding-test-php/assets/112695126/39592c12-cd15-4ff3-ac26-9479d8ce5139)

- Show Article Details by ID
```
Guest users & Logged in users both can see the article Details
```
`GET http://localhost:34251/articles/4.json`
![details_page](https://github.com/irfanmominmt/coding-test-php/assets/112695126/7fa77d6d-dd09-42e1-a481-c233d4c6036c)

- Update an Article
```
Only Logged in user and authorized user can update the Article
```
`POST http://localhost:34251/articles/update/5.json`
```
{
  "title": "Article Updated",
  "body": "Article Body Updated"
}
```
![Update](https://github.com/irfanmominmt/coding-test-php/assets/112695126/66d64d75-6db7-4d89-ae85-0f7688c7a12a)

- Delete an Article
```
Only Logged in user and authorized user can delete the Article
```
`DELETE http://localhost:34251/articles/delete/2.json`
![deleted](https://github.com/irfanmominmt/coding-test-php/assets/112695126/f955bb2a-ceb7-4f6e-8b23-944529b661dd)

### Like Feature

- Like an Article
```
Any Logged in user can like to any Article
```
- Request Body
```
{
  "art_id": 5
}
```
`POST http://localhost:34251/article/like`
![liked](https://github.com/irfanmominmt/coding-test-php/assets/112695126/b4c655d3-50ca-4bac-828b-ec0daaeda754)

- Already liked Message
```
User can like only once to any specific article.
```
![already](https://github.com/irfanmominmt/coding-test-php/assets/112695126/8d7fbaac-6635-439b-b5a7-565058896eb7)

