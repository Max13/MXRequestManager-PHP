MXRequestManager-PHP
====================

Note
----
This is an old README, when the lib wasn't installable by composer. I'm on it, there are only some changes related to autoload, 

Required
--------
- PHP >= `5.3`
- php-curl (with SSL if required)
- APIs which return `JSON` encoded data (More structures are coming)
- Brain >= `Working`

How to download
---------------
There are several ways to download **MxRequestManager-PHP**:

- Install with composer (`"max13/rest-manager": "dev-master"`)
- Clone the [github repository](https://github.com/Max13/MXRequestManager-PHP) with `git clone <repo> [<dest>]`
- Download the zip file on [github](https://github.com/Max13/MXRequestManager-PHP) directly
- Try to find another one by yourself :/

Then place it where you want (readable location, in order to load it).

How to use
----------
Captain Obvious is so Obvious, that this is truly the most interesting part of the README.

You can find a Doxygen doc somewhere, you'll just see how to basically use the library.

First of all, let's take a simple example. You have your APIs (`api.awsome-guy.com`) and you want to retrieve a member list from your **users** resource, with a **GET**.

JSON string would be:

```
{
	users:[
		{
			"id": 4,
			"username": "foo"
		}, {
			"id": 12,
			"username": "bar"
		}
	]
}
```

```
<?php
// Adapt the path to your installation
require_once('MXRequestManager-PHP/MXRequestManager.php');

// Instanciate the manager, without the triling slash
$mx = new MXRequestManager('http://api.awsome-guy.com');

// Prepare your parameters, i.e a token
$params = array('token' => 'abcdefg');

// Make the request and return the status
$res = $mx->get('/users', $params);
?>
```

From there, you must verify `$res` because it can be 3 types (so check the type too with `===`)

```
// MXRequestManager error,
// errno are in the top of the lib file
if ($res === FALSE)
	die('Client error: '.$mx->errno());
if ($res === TRUE) // No JSON, may be a PHP Error
	die('Parse error: '.$mx->response('body'));
```
And after that and your own checks (for example, if the property `errors` is present), you can safely use $res as a `stdClass`:

```
<?php
echo 'User n0: ' . $res->users[0]->id . "<br />\n";
foreach ($res->users as $user)
	echo $user->id . ' / ' . $user->username . "<br />\n";
?>
```
And this example will output:

```
User n0: 4
4 / foo
12 / bar
```
That's it. You can already use `MXRequestManager` !

How to check the entire response ?
----------------------------------
This is simple, you can call:

```
<?php
echo $mx->rawResponse();
?>
```

How to check the headers ?
--------------------------
`MXRequestManager` is intelligent and smart enough to allow you to check the headers simply.

Here is an example header:

```
HTTP/1.1 200 OK
Server: nginx
Date: Mon, 04 Feb 2013 21:49:22 GMT
Content-Type: application/json; charset=utf-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/5.3.21
Content-Encoding: gzip
Vary: Accept-Encoding
```

When processed, every line are split and stored in an array, accessible by a key, corresponding to the part before the semicolon (`:`) of each line, except for the first line which has for key `Status` and the `HTTP code` accessible with in `Code`.

There is an internal multi-dimentional array which contains 2 root keys: `headers` and `body`:

- `headers` contains an associative array of the header's values
- `body` is the body returned as a `string`

Nothing better than an example:

```
<?php
// Adapt the path to your installation
require_once('MXRequestManager-PHP/MXRequestManager.php');

// Instanciate the manager, without the triling slash
$mx = new MXRequestManager('http://api.awsome-guy.com');

// Prepare your parameters, i.e a token
$params = array('token' => 'abcdefg');

// Make the request and return the status
$res = $mx->get('/users', $params);

// MXRequestManager error,
// errno are in the top of the lib file
if ($res === FALSE)
	die('Client error: '.$mx->errno());
if ($res === TRUE) // No JSON, may be a PHP Error
	die('Parse error: '.$mx->response('body'));

echo "HTTP Status: " . $mx->response('headers', 'Status');
echo "<br />\n";
echo "HTTP Code: " . $mx->response('headers', 'Code');
// Since PHP 5.4 you can use $mx->response()['headers']
echo "<br />\n";
echo "Response body: " . $mx->response('body');
?>
```

Additional notes
-------------------------
A sort of manual will come soon...