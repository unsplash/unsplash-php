# PHP Unsplash Wrapper

[ ![Codeship Status for CrewLabs/Unsplash-PHP](https://codeship.com/projects/60048560-0bba-0133-b04d-265ef25499ca/status?branch=master)](https://codeship.com/projects/90915)

A php wrapper to connect and interact with the Unsplash API.

## Installation

The php wrapper is a composer package. Only add this line to your composer.json

```
{
    "require": {
        "crewlabs/unsplash"
    }
}
```

And update your composer.lock file

`php composer.phar update`

## Usage

### Configuration

You need to configure the wrapper before being able to use it. In a file that is call at every request *(i.e. The file who called the vender/autoload.php)*

```
Crew\Unsplash\HttpClient::$connection = Crew\Unsplash\Connection(
	new Crew\Unsplash\Provider([
			'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none'
	]);
);
```

### Private Scopes

To access the method that are private, you need to retrieve an access token first.

#### Retrieve the authorization url

When you want to generate the authorization url, you need to specify which scope you want access to.

*The current scopes are*

- public (Access user's public data)
- read_user (Access user's private data)
- write_user (Edit and create user data)
- read_photos (Access private information from user photos)
- write_photos (Post and edit photos for users)


```
$scopes = ['public', 'write_user']
Crew\Unsplash\HttpClient::$connection->getConnectionUrl($scopes);
```

### Authorize the application

You need to retrieve the code returned by the authorization url and use it to generate an access token

`Crew\Unsplash\HttpClient::$connection->generateToken($code);`

From this point, you can execute method that need private scope

### API methods

Some parameters are identical accross all the methods.

`$page` Define the page on which the elements are retrieve. *Default 1*

`$per_page` Define the elements presents by page. *Default 10*

The method that return multiple object, actually return an ArrayObject, which can act like a normal array.

#### Category
Retrieve informations related to the categories

`Crew\Unsplash\Category::all($page, $per_page);`

`Crew\Unsplash\Category::find(integer $id);`

```
$category = Crew\Unsplash\Category::find(integer $id);
$photos = $category->photos($page, $per_page)
```

#### Curated Batch
Retrieve informations related to the curated batches

`Crew\Unsplash\CuratedBatch::all($page, $per_page);`

`Crew\Unsplash\CuratedBatch::find(integer $id);`

```
$batch = Crew\Unsplash\CuratedBatch::find(integer $id);
$photos = $batch->photos($page, $per_page);
```

#### Photo
Retrieve informations related to the photos

`Crew\Unsplash\Photo::all($page, $per_page);`
`Crew\Unsplash\Photo::search(string $search, integer $category_id, $page, $per_page);`
`Crew\Unsplash\Photo::find(string $id);`
`Crew\Unsplash\Photo::create(string $file_path);`

```
$photo = Crew\Unsplash\Photo::find(string $id);
$photo->photographer();
```

#### User
Retrieve information related to a user

`Crew\Unsplash\User::find($username)`

```
$user = Crew\Unsplash\User::find($username);
$user->photos($page, $per_page);
```

```
$user = Crew\Unsplash\User::current();
$user->update([$key => value]);
```

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/CrewLabs/Unsplash-PHP. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant code](http://contributor-covenant.org/) of conduct.