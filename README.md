# QueryServer (virion + plugin)

Two deliverables in one repo:
- **QueryServerCore (virion/library)**: async HTTP (api.mcsrvstat.us v3) + UDP fallback (libpmquery) with a tiny callback-based API for other plugins.
- **QueryServer (plugin)**: thin wrapper exposing `/query` command that uses the core.

## Features
- Async HTTP query (no main-thread blocking) with automatic UDP fallback.
- Embedded, namespaced **libpmquery**.
- Callback API for other plugins (no promises/futures needed).

## Command (plugin)
- `/query <domain/ip[:port]> [port]`  
  Example: `/query test.pmmp.io 19132`

## API Usage (virion)
```php
use NhanAZ\QueryServer\Main;

$api = Main::getInstance()->getApi();
$api->query("test.pmmp.io", 19132, function(array $result): void {
    if (($result['ok'] ?? false) === true) {
        // $result['source'] === 'api' or 'udp'
        // $result['data']  : API status object (api) or UDP array (udp)
    } else {
        // $result['error']
    }
});
```

## Installation
### As plugin
1. Download the `QueryServer` phar.
2. Drop into `plugins/` (PocketMine-MP 5.x / PHP 8.1+).

### As virion
Add dependency in `.poggit.yml`:
```yaml
libs:
  - src: NhanAZ-Libraries/QueryServer/QueryServerCore
    version: ^0.0.3
```

### Example consumer plugin
- See `examples/QueryConsumer` for a minimal plugin that depends on `QueryServer` and runs `/qtest` to query `test.pmmp.io:19132` via the API.

## License & Attribution
- LGPL-3.0-or-later.
- Bundled **libpmquery** (LGPL-3.0-or-later) by [jasonw4331/libpmquery](https://github.com/jasonw4331/libpmquery).
