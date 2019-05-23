# http
curl library for php

### Requirement

1. PHP >= 5.6
2. guzzlehttp >= 6.3.3
3. **[Composer](https://getcomposer.org/)**

## Installation

```shell
$ composer require opensite/http
```

### Usage
```
<?php

use opensite\http;


# GET 请求

Http::HTTPRequest('GET',['name' => 'test']);


# POST 请求

Http::HTTPRequest('POST',['name' => 'test']);

```


## Contributors

[Your contributions are always welcome!](https://github.com/openset/http/graphs/contributors)

## LICENSE

Released under [MIT](https://github.com/openset/http/blob/master/LICENSE) LICENSE
