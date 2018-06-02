# symfony-graphql-users-api-starter

A demo application using:
* Symfony4
* GraphQL (overblog/graphql-bundle)
* JWT (lexik/jwt-authentication-bundle)

Notes on implementation:
* ADR pattern for resolvers/mutators
* Behat tests
* Api lives under /api
* GraphiQL enabled by default /graphiql
* User entity uses ramsey/uuid for Id
* User entity and security decoupled see [blog post](https://stovepipe.systems/post/decoupling-your-security-user)
* Entity validation moved to forms, see [blog post](https://blog.martinhujer.cz/symfony-forms-with-request-objects/)
* Based on Cyclepath-Symfony [repo](https://github.com/GuikProd/CyclePath-Symfony)
