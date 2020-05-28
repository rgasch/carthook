# carthook Test Project

## Basic CS Tests

The code for these tests can be found in the /basicCS folder. Each file is a standalone 
PHP file (ie: no Laravel required) specific for one of the tests. All files contain quite 
a bit of comments which explain how each task was solved and some of the design 
decisions/tradeoffs which went into the implementation choices. So please refer to these 
files for the based CS Challenges.

## Advanced/Practical (API Implementation)

This is a standard API implementation for Laravel which contains the following
parts: 
  1) API routes
  2) Database Migrations
  3) Custom config file (**/config/carthook.php**)
  4) Controller
  5) Models
  6) Repositories
  

### Detailed info about points 1-5 above: 

1) API Routes: the following routes have been implemented (all routes are GET) and return JSON data
  - */api/users*: get all users, store them in local DB
  - */api/users/{123}* OR */api/users/someEmail@domain.com*: find a specfic user in local DB by ID or email.
  - */api/users/{123}/posts*: get posts for a specific user-ID (either from API or local DB)
  - */api/users/{123}/posts/{searchText}*: get posts from local DB for a specific user-ID where the post title contains the specified searchText
  - */api/posts/{searchText}*: get posts from local DB where the post title contains the specified searchText
  - */api/posts/{345}/comments*: get comments for the specified post-id either from API or local DB
  
2) Database Migrations: nothing noteworthy to report here. Indexes (and a fulltext index on post title) are created as requried by the usage scenarios outlined in your instructions.

3) Custom Config File: 
  - base API URL we get data from 
  - cache levels for local DB per object type
  
4) Controller: the controller code is very small/lightweight, all relevant logic has been encapsulated into the appropriate 
Model and Repository classes. This allows for functional encapsulation & isolation, better code quality, better 
reusability and better testability. 

5) Models: The basic Eloquent Models are used to define the ORM classes encapsulating the SQL/CRUD operations against 
each table. In addition to this, the Models also define the relationships between the different model classes so 
that Eloquent can auto-fetch related objects without us having to write explicit code to do so. 

6) Repositories: 
  - This is where the real 'meat' (so to speak) of the application resides. Repositories wrap 
    Model classes and add application specific logic. The idea behind this is that the models themselves 
    should not contain application logic and that this approach allows us to create specific repositories
    based upon the same object without having to alter the base/core model. 
  - Also, for this assignment I created an abstract base repository class which implements some common 
    methods which are used by all repositories and provide an abstract method signature for a method 
    to fetch data from the API and save it to the local DB.
    
## Thoughts/Comments/FuturePlans: 

