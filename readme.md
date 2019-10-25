Get the current user IP in a quick way:

```php
use Oriceon\Ip;
<?php
$ip = Ip::get();
echo "Hello, your ip is {$ip}";
?>
```

Install using composer. Package name is ```oriceon/ip```.

Add this to **composer.json**
```json
"require": {
        "oriceon/ip": "~1.0"
    }
```
