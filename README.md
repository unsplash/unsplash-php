# PHP Unsplash Wrapper

[ ![Codeship Status for CrewLabs/Unsplash-PHP](https://codeship.com/projects/60048560-0bba-0133-b04d-265ef25499ca/status?branch=master)](https://codeship.com/projects/90915)
[![Dependency Status](https://www.versioneye.com/php/crewlabs:unsplash/badge?style=flat)](https://www.versioneye.com/php/crewlabs:unsplash)

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

### Permission Scopes

The current permission scopes defined by the [Unsplash API](https://unsplash.com/documentation#authorization) are:

- `public` (Access a user's public data)
- `read_user` (Access a user's private data)
- `write_user` (Edit and create user data)
- `read_photos` (Access private information from a user's photos)
- `write_photos` (Post and edit photos for a user)
- `write_likes` (Like a photo for a user)

If you're only using the `public` permissions scope (i.e. nothing requiring a specific logged-in user), you're ready to go!

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

### API methods

For more information about the the responses for each call, refer to the [official documentation](https://unsplash.com/documentation).

Some parameters are identical across all methods:

  param     | Description
------------|-----------------------------------------------------
`$per_page` | Defines the number of objects per page. *Default 10*
`$page`     | Defines the offset page. *Default 1*

*Note: The methods that return multiple objects return an `ArrayObject`, which acts like a normal array.*

#### Category

Retrieve category information:

```php
Crew\Unsplash\Category::all($page, $per_page);
```

```php
Crew\Unsplash\Category::find(integer $id);
```

```php
$category = Crew\Unsplash\Category::find(integer $id);
$photos = $category->photos($page, $per_page)
```

#### Curated Batch

Retrieve curated batch information:

```php
Crew\Unsplash\CuratedBatch::all($page, $per_page);
```

```php
Crew\Unsplash\CuratedBatch::find(integer $id);
```

```php
$batch = Crew\Unsplash\CuratedBatch::find(integer $id);
$photos = $batch->photos($page, $per_page);
```

#### Photo

Retrieve photo information:

```php
Crew\Unsplash\Photo::all($page, $per_page);
```

```php
Crew\Unsplash\Photo::search(string $search, integer $category_id, $page, $per_page);
```

```php
Crew\Unsplash\Photo::find(string $id);
```

```php
Crew\Unsplash\Photo::create(string $file_path);
```

```php
$photo = Crew\Unsplash\Photo::find(string $id);
$photo->photographer();
```

```php
Crew\Unsplash\Photo::random();

// Or apply some optional filters by passing a key value array of filters
$filters = [
    'category' => [3, 6],         // string|array Retrieve photos matching the category ID/IDs.
    'featured' => true,           // boolean Limit selection to featured photos.
    'username' => 'andy_brunner', // string Limit selection to a single user.
    'query'    => 'coffee',       // string Limit selection to photos matching a search term..
    'w'        => 100,            // integer Image width in pixels.
    'h'        => 100,            // integer Image height in pixels.
];
Crew\Unsplash\Photo::random($filters);
```

For more information regarding filtering, [refer to the Offical documentation](https://unsplash.com/documentation#get-a-random-photo).

```php
$photo = Crew\Unsplash\Photo::find(string $id);
$photo->like();
$photo->unlike();
```

#### User

Retrieve user information:

```php
Crew\Unsplash\User::find($username)
```

```php
$user = Crew\Unsplash\User::find($username);
$user->photos($page, $per_page);
```

```php
$user = Crew\Unsplash\User::current();
$user->update([$key => value]);
```

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/CrewLabs/Unsplash-PHP. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant](http://contributor-covenant.org/) code of conduct.
