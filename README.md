# PHP Unsplash Wrapper

[ ![Codeship Status for CrewLabs/Unsplash-PHP](https://codeship.com/projects/60048560-0bba-0133-b04d-265ef25499ca/status?branch=master)](https://codeship.com/projects/90915)
-[![Dependency Status](https://www.versioneye.com/php/crewlabs:unsplash/badge?style=flat)](https://www.versioneye.com/php/crewlabs:unsplash)

A PHP client for the [Unsplash API](https://unsplash.com/documentation).

- [Official documentation](https://unsplash.com/documentation)
- [Changelog](https://github.com/CrewLabs/Unsplash-PHP/blob/master/CHANGELOG.md)

## Installation

`Unsplash-PHP` uses [Composer](https://getcomposer.org/). To use it, require the library

```
composer require crewlabs/unsplash
```

## Usage

### Configuration

Before using, configure the client with your application ID and secret. If you don't have an application ID and secret, follow the steps from the [Unsplash API](https://unsplash.com/documentation#creating-a-developer-account) to register your application.

Note that if you're just using actions that require the [public permission scope](#permission-scopes), only the `applicationId` is required.

```php
Crew\Unsplash\HttpClient::init([
	'applicationId'	=> 'YOUR APPLICATION ID',
	'secret'		=> 'YOUR APPLICATION SECRET',
	'callbackUrl'	=> 'https://your-application.com/oauth/callback'
]);
```
### Authorization workflow
To access actions that are non-public (i.e. uploading a photo to a specific account), you'll need a user's permission to access their data. Direct them to an authorization URL (configuring any scopes before generating the authorization URL):

```php
$scopes = ['public', 'write_user']
Crew\Unsplash\HttpClient::$connection->getConnectionUrl($scopes);
```

Upon authorization, Unsplash will return to you an authentication code via your OAuth
callback handler. Use it to generate an access token:

```php
Crew\Unsplash\HttpClient::$connection->generateToken($code);
```

With the token you can now access any additional non-public actions available for the authorized user.


#### Permission Scopes

The current permission scopes defined by the [Unsplash API](https://unsplash.com/documentation#authorization) are:

- `public` (Access a user's public data)
- `read_user` (Access a user's private data)
- `write_user` (Edit and create user data)
- `read_photos` (Access private information from a user's photos)
- `write_photos` (Post and edit photos for a user)
- `write_likes` (Like a photo for a user)

===

### API methods

For more information about the the responses for each call, refer to the [official documentation](https://unsplash.com/documentation).

Some parameters are identical across all methods:

  param     | Description
------------|-----------------------------------------------------
`$per_page` | Defines the number of objects per page. *Default 10*
`$page`     | Defines the offset page. *Default 1*

*Note: The methods that return multiple objects return an `ArrayObject`, which acts like a normal stdClass.*

===

### Category


#### Crew\Unsplash\Category::all()
Retrieve the list of categories

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$per_page`    | int  | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int  | Opt *(Default: 1)*

**Example**

```php
Crew\Unsplash\Category::all($page, $per_page);
```

===

#### Crew\Unsplash\Category::find($id)
Retrieve a specific category

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$id`          | int  | Required

**Example**

```php
Crew\Unsplash\Category::find(integer $id);
```

===

#### Crew\Unsplash\Category::photos($page, $per_page)
Retrieve photos from a specific category.

*Note:* You need to instantiate a category object first.

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$per_page`    | int  | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int  | Opt *(Default: 1)*

**Example**

```php
$category = Crew\Unsplash\Category::find(integer $id);
$photos = $category->photos($page, $per_page)
```

===

### Curated Batch

#### Crew\Unsplash\CuratedBatch::all($page, $per_page)

Retrieve the list of curated batches

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$per_page`    | int  | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int  | Opt *(Default: 1)*

**Example**


```php
Crew\Unsplash\CuratedBatch::all($page, $per_page);
```

===

#### Crew\Unsplash\CuratedBatch::find($id)
Retrieve a specific curated batch

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$id`          | int  | Required

**Example**

```php
Crew\Unsplash\CuratedBatch::find(integer $id);
```

===

#### Crew\Unsplash\CuratedBatch::photos($page, $per_page)
Retrieve photos from a curated batch.

*Note:* You need to instantiate a curated batch object first.

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$per_page`    | int  | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int  | Opt *(Default: 1)*

**Example**

```php
$batch = Crew\Unsplash\CuratedBatch::find(integer $id);
$photos = $batch->photos($page, $per_page);
```

===

### Photo

#### Crew\Unsplash\Photo::all($page, $per_page)
Retrieve a list of photos

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$per_page`    | int  | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int  | Opt *(Default: 1)*

**Example**

```php
Crew\Unsplash\Photo::all($page, $per_page);
```

===

#### Crew\Unsplash\Photo::search($keyword, $category_id, $page, $per_page);

Retrieve photos from a search by keyword and category

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$keyword`     | string | Opt
`$category_id` | string | Opt
`$per_page`    | int    | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int    | Opt *(Default: 1)*

**Example**

```php
Crew\Unsplash\Photo::search(string $search, integer $category_id, $page, $per_page);
```

===

#### Crew\Unsplash\Photo::find($id)
Retrieve a specific photo

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$id`          | int  | Required

**Example**

```php
Crew\Unsplash\Photo::find($id);
```

===

#### Crew\Unsplash\Photo::create($file_path)
Post a photo on the user behalf.

*Note:* You need the `write_photos` permission scope

**Arguments**

  Argument     | Type   | Opt/Required
---------------|--------|--------------
`$file_path`   | string | Required

**Example**

```php
Crew\Unsplash\Photo::create( $file_path);
```

===

#### Crew\Unsplash\Photo::photographer()
Retrieve the photo's photographer

*Note:* You need to instantiate a photo object first

**Arguments**

*N/A*

**Example**


```php
$photo = Crew\Unsplash\Photo::find(string $id);
$photo->photographer();
```

===

#### Crew\Unsplash\Photo::random([category => $value, featured => $value, username => $value, query => $value, w => $value, h => $value])
Retrieve a random photo from specified filters. For more information regarding filtering, [refer to the Offical documentation](https://unsplash.com/documentation#get-a-random-photo).


*Note:* An array need to be passed as a parameters.

**Arguments**


  Argument     | Type | Opt/Required
---------------|------|--------------
category | array | Opt *(Retrieve photos matching the category ID/IDs)*
featured | boolean | Opt *(Limit selection to featured photos)*
username | string | Opt *(Limit selection to a single user)*
query | string | Opt *(Limit selection to photos matching a search term)*
w | int | Opt *(Image width in pixels)*
h | int | Opt *(Image height in pixels)*


**Example**


```php

// Or apply some optional filters by passing a key value array of filters
$filters = [
    'category' => [3, 6],
    'featured' => true,
    'username' => 'andy_brunner',
    'query'    => 'coffee',
    'w'        => 100,
    'h'        => 100
];
Crew\Unsplash\Photo::random($filters);
```

===

#### Crew\Unsplash\Photo::like()
Like a photo on the user behalf

*Note:* You need to instantiate a photo object first

*Note:* You need the `like_photos` permission scope

**Arguments**

*N/A*

**Example**


```php
$photo = Crew\Unsplash\Photo::find(string $id);
$photo->like();
```

===

#### Crew\Unsplash\Photo::unlike()
Unlike a photo on the user behalf

*Note:* You need to instantiate a photo object first

*Note:* You need the `like_photos` permission scope

**Arguments**

*N/A*

**Example**


```php
$photo = Crew\Unsplash\Photo::find(string $id);
$photo->unlike();
```

===

### User

#### Crew\Unsplash\User::find($username)
Retrieve a user information

**Arguments**

  Argument     | Type   | Opt/Required
---------------|--------|--------------
`$username`    | string | Required

**Example**

```php
Crew\Unsplash\User::find($username)
```

===

#### Crew\Unsplash\User::current()
Retrieve the user's private information

*Note:* You need the *read_user* permission scope

**Arguments**

*N/A*

**Example**

```php
$user = Crew\Unsplash\User::current();
```

===

#### Crew\Unsplash\User::photos($page, $per_page)
Retrieve user's photos

*Note:* You need to instantiate a user object first

**Arguments**

  Argument     | Type | Opt/Required
---------------|------|--------------
`$per_page`    | int  | Opt *(Default: 10 / Maximim: 30)*
`$page`        | int  | Opt *(Default: 1)*

**Example**

```php
$user = Crew\Unsplash\User::find($username);
$user->photos($page, $per_page);
```

===

#### Crew\Unsplash\User::update([$key => value])
Update current user's fields. Multiple fields can be pass in the array.

*Note:* You need to instantiate a user object first

*Note:* You need the *write_user* permission scope.

**Arguments**

  Argument     | Type   | Opt/Required | Note  |
---------------|--------|--------------|-------|
`$key`         | string | Required     | The following keys are accepted: `username`, `first_name`, `last_name`, `email`, `url`, `location`, `bio`, `instagram_username`
`$value`       | mixed  | required

```php
$user = Crew\Unsplash\User::current();
$user->update(['first_name' => 'Elliot', 'last_name' => 'Alderson']);
```

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/CrewLabs/Unsplash-PHP. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant](http://contributor-covenant.org/) code of conduct.
