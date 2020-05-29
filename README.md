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
  - API timeout (in seconds)
  
A fundamental concept I adhere to is that it's preferable to have code which can be configured rather than code 
which needs to be altered to adapt to changing conditions. These settings could even be moved to the .env file, 
but I have opted not to do so for this case, since they adhere to the basic assignment given and there should not 
be a reason to change them frequently.
  
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
    
Basic design consideration for local data fetching: While the Models define relationships, we currently 
don't use them. The reason for this is, that sicne we are receiving the ID to query from the API call, 
querying posts and comments directly saves us 1 SQL call (since, for example, we don't have to fetch the 
post to then get the comments via the relationship). The relationships have however been implement because 
they document the data structure on a code level and because they could be useful in other applications 
based on this code (such as a web front-end).

Please note: the database field naming is not a 1-to-1 mapping to fields received from the API. The reason 
for this is that I prefer to have my database fields in the naming convention "lowercase_name_with_underscores"
because this makes it clear when a field is from the local DB vs. when it has been recived from the API. This 
is a design consideration which might have been done differently based upon the code standard you prefer; it's 
simply a matter of preference which has both advantages and drawbacks. 

Please note (part2): There is the duality of local IDs and external IDs. It should be noted that for querying objects 
we rely on the field 'id_external' which captures the ID received from the API. 

Please Note (part 3): my code contains a significant amount of comments; please refer to these for a more 
detailed discussion on some of the design choices that were made. 

## Thoughts/Comments/FuturePlans: 

1) There are obviously some serious shortcomings in the current design of the API implementation. 

  - We only cache 50 records for posts and comments; the case where more data is available is currently not considered
  - There is no logic/code to handle local DB cache invalidation. Depending on the the amount of data a real API would 
    this is something which should be considered, so that we don't store terabytes of maybe years-old data which is  
    never queried.
  - It can be assumed that eventually this API might be hooked up to a front-end which then would allow data creation
    (users, posts, comments) through POST requests. This is currently not implemented because it was not part of the 
    assignment. 
  - Error handling could be improved: I implemented a generic error handler which returns an error in JSON format, 
    but currently this is very basic and doesn't give very informative error messages. This is definitely something 
    which could be improved, but I'd say that's beyond the scope of a test assignment where we're working against 
    a well defined set of constraints.
    
