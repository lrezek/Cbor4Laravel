About
=============

Cbor4Laravel adds CBOR functionality to Laravel Requests and Responses. For more information on what CBOR is, please see the [spec](http://cbor.io/).

Installation
=============

Add `lrezek/cbor4laravel` as a requirement to `composer.json`:

```JavaScript
{
    "require": {
       "lrezek/cbor4laravel": "dev-master"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

Request
=============
Because the Request class is made so early in the Laravel lifecycle, you need to override the implementation in `bootstrap/start.php`. Add the following to the start of the file:

```PHP
use Illuminate\Foundation\Application;

Application::requestClass('LRezek\Cbor4Laravel\Http\Request');
```

This enables the following 3 methods:

```PHP

Request::isCbor();    //Returns true if the request is in CBOR format, false otherwise.
Request::wantsCbor(); //Returns true if the accept type of the request is CBOR, false otherwise.
Request::cbor();      //Returns the decoded request content.

```

Please note that all three of these methods are also available in the `Input` Facade, as `Input` and `Request` are the same object in Laravel.

As with `Input::json()`, `Input::cbor()` allows you to get CBOR decoded data in a `ParameterBag`. This means you can specify a key and a default value to get, or get all the input as an array:

```PHP
Input::cbor()->all();         //Get all input as an array.
Input::cbor($key);            //Get value of the specified key in the input.
Input::cbor($key, $default);  //Get value of specified key in the input, or the specified $default if the key isn't found.
```

Response
=============
To enable CBOR responses, simply replace Laravels `Reponse` Facade in the `aliases` array of `app/config/app.php`:

```PHP
  //'Response'      => 'Illuminate\Support\Facades\Response',
    'Response'      => 'LRezek\CBOR4Laravel\Facades\Response',
```

This allows you to use `Response::cbor()` the same way you would use `Response::json()`. For an example of this, please refer to the [Laravel documentation](http://laravel.com/docs/4.2/responses#special-responses).

Additionally, you can make a custom response and format it in CBOR as follows:

```PHP
Response::make($content)->format('cbor'); //Returns a CBOR formatted Response.
```
