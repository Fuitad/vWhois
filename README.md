# PHP Whois Client and Parser

This library is very heavily based on the Ruby libraries [whois](https://raw.githubusercontent.com/weppos/whois) and [whois-parser](https://github.com/weppos/whois-parser). It's a work in progress and a lot of parsers are not yet available.

I know there are other whois libraries for PHP that exists but I don't like how they returns the data. A cleaner and standard structure was important for me so I decided to create this package.

Absolutely not ready for production usage yet. I might make some breaking changes in the upcoming weeks. Use at your own risk.

## Requirements

* PHP >= 5.4.0

## Installation

```shell
composer require fuitad/vwhois
```

## How to use

```php
use vWhois\vWhois;

$vWhois = new vWhois();

$whoisResult = $vWhois->lookup('google.com');
```

## Credits

Again, this library wouldn't exist without the awesome [whois](https://raw.githubusercontent.com/weppos/whois) and [whois-parser](https://github.com/weppos/whois-parser) ruby gems.

## Contributing

[Pull requests](https://github.com/weppos/whois/pulls) are welcome. So many parsers are needed for all them TLDs O_o.

## License

This is Free Software distributed under the MIT license.