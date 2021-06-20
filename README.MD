# Router
A simple routing Utility
# **How to use**
This library leaves it up to you how you want to handle the resulting callback, focusing only on routing
## 1. **Adding new Routes**
This library provides three ways to add new Routes to Router.
**Starting with the most recommended way at 1.2**
### **1.1. Route creation schema**
Schema is almost the same for each of these methods
1. ***path*** - String with path that the router will match. It **MUST** end with a forwards backslash to work correctly. For segments with variable arguments contain the name of argument with curly brackets (eg. *`'/offer/{id}'`*) 

2. ***callback*** - Array containing name of the class / object instance that the Route will be refering to (eg. *`[IndexController::class, "index"]`*).                        It may also be an anonymous function (eg. *`fn() => print("Hello from anonymous function!")`*)

3. (optional) ***methods*** - Array containg **uppercased** names of http methods that this route will correspond to (eg. *`["POST", "OPTION"]`*). Defaults to `["GET", "HEAD"]`

4. (optional) ***params*** - Associative array with argument names as keys and regexes that they match as values. If you used an argumented segment in path, you must provide a regex for it there (eg. *`["id" => "\d+"]`*)

5. (optional) ***defaults*** - Associative array with argument names as keys and defaulting values as value. You may provide them for some of argumented segments (eg.*`["id" => 1]`*)

### **1.2. Loading from an associative array**
Create new instance of Router and then call that instance's *`Router::insertConfig`* method,
passing associative array with Route's name as key, and value of an array containg the rest of route data.
eg.
```php
    use SunnyFlail\Router\Router;
    $router = new Router();
    $router->insertConfig([
        "index" => [
            "/",
            fn() => print("Talking to you from '/'!"),
        ] 
    ]);
```

### **1.3. Creating Route objects and inserting them**
Create a Route object and call `Router::addRoutes` providing it as an argument.
You may insert as many Routes as you want at one call, just separate them with a comma.
eg.
```php
    use SunnyFlail\Router\Route;
    $routeOne = new Route("index", "/", fn() => print("Hello world, from index!"));
    $routeTwo = new Route(
        "offer", "/offer/{id}",
        fn($id) => printf("Hello world, from offer number %s!", $id),
        ["GET", "HEAD"],
        ["id" => "\d+"],
        ["id" => 1]
    );
    $router->addRoutes($routeOne, $routeTwo);
```

### **1.4. Manually inserting Route data**
You may also manually insert the data by calling `Router::addRoute` method.
It's syntax is the same as when creating new Route object
eg.
```php
    $router->addRoute("login", "/login/", [LoginController::class, "login"]);
```

## **2. Routing**
To begin routing invoke *`Router::match`*, providing uppercase *HTTP request method* as first argument, and *request path* as second.
eg. 
```php
    $matchedData = $router->match($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_PATH"]);
```
This method returns an instance of *`SunnyFlail\Router\MatchedData`* class containing matched Route and data scraped from argumented segments.

To get these data invoke *`MatchedData::getData`* method. It returns an associative array with parameter names as keys, and scraped values corresponding to the key. It may return an empty array if there were no argumented params

To get matched route, invoke *`MatchedData::getRoute`*

The *`Router::match`* method may throw a *`SunnyFlail\Router\NotFoundException`* if no routes were matched
## **3. Operating on inserted Routes**
### **3.1. Getting a Route object from Router**
To get a route call *`Router::getRoute`*, providing the name of searched route as an argument.
eg.
```php
    $route = $router->getRoute("offer");
```
### **3.2. Generating urls**
To generate relative (to the domainname) url pointing to selected route call *`Route::generateUrl`* method. You may need to provide an associative array to fill the parameters
eg.
```php
    $url = $route->generateUrl(["id" => 2]);
```