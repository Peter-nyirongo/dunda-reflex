```php
<?php

session_start();

$_SESSION = [];

session_destroy();

header("Location: /dunda-reflex/auth/login.php");
exit;

?>
```
