# PHP Unsplash Wrapper

[ ![Codeship Status for CrewLabs/Unsplash-PHP](https://codeship.com/projects/60048560-0bba-0133-b04d-265ef25499ca/status?branch=master)](https://codeship.com/projects/90915)

A PHP client for the [Unsplash API][official documentation].

## Installation

`Unsplash-PHP` uses [Composer](https://getcomposer.org/). To use it, require the library

```
composer require crewlabs/unsplash
```

## Usage

### Configuration

Before using, configure the client with your application ID and secret. If you don't have an application ID and secret, follow the steps from the [Unsplash API](official documentation) to register your application.

```php
Crew\Unsplash\HttpClient::$connection = Crew\Unsplash\Connection(
	new Crew\Unsplash\Provider([
		'clientId'     => 'YOUR APPLICATION ID',
		'clientSecret' => 'YOUR APPLICATION SECRET',
		'redirectUri'  => 'https://your-application.com/oauth/callback'
	]);
);
```

### Scopes

The current scopes defined by the [Unsplash API](official documentation) are:

- `public` (Access a user's public data)
- `read_user` (Access a user's private data)
- `write_user` (Edit and create user data)
- `read_photos` (Access private information from a user's photos)
- `write_photos` (Post and edit photos for a user)

If you're only using the `public` scope (i.e. nothing requiring a specific logged-in user), you're ready to go!

To access actions that are non-public (i.e. uploading a photo to a specific account), you'll need a user's permission to access their data. Direct them to an authorization URL (configuring any scopes before generating the authorization URL):

```php
$scopes = ['public', 'write_user']
Crew\Unsplash\HttpClient::$connection->getConnectionUrl($scopes);
```

Upon authorization, Unsplash will return to you an authentication code via your OAuth
callback handler. With that you can generate an access token:

```php
Crew\Unsplash\HttpClient::$connection->generateToken($code);
```

With the token you can now access any additional non-public actions available for the authorized user.

### API methods

Some parameters are identical accross all methods:

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

Bug reports and pull requests are welcome on GitHub at https://github.com/CrewLabs/Unsplash-PHP. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant code](http://contributor-covenant.org/) of conduct.


[official documentation]: https://unsplash.com/documentation
