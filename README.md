# twsms-sender

最小可用的 TWsms 發送套件（PHP 8.2+）。

## 安裝

```bash
composer require platolin/twsms-sender
```

## 最小使用範例

```php
<?php

declare(strict_types=1);

use Twsms\Exception\TransportException;
use Twsms\Exception\ValidationException;
use Twsms\TwsmsSender;

$sender = new TwsmsSender();

try {
    $result = $sender->send('username', 'password', '0912345678', '測試簡訊');

    if ($result->isSuccess()) {
        echo 'msgid=' . ($result->messageId ?? '');
    }
} catch (ValidationException $e) {
    // 輸入驗證失敗
    echo $e->getMessage();
} catch (TransportException $e) {
    // 上游連線/回應錯誤
    echo $e->getMessage();
}
```

## 錯誤處理

- `ValidationException`：輸入不符規則（空值、手機格式、訊息長度）。
- `TransportException`：上游連線失敗、HTTP 狀態異常、回應 JSON 格式錯誤。

## Public API 穩定性與 SemVer

- `TwsmsSender`、`SendResult`、`ValidationException`、`TransportException` 屬於公開 API。
- `Twsms\Internal\*` 屬 internal，不納入相容性承諾。
- 版本採 SemVer：
  - PATCH：修正不改變公開 API 行為。
  - MINOR：向後相容新增。
  - MAJOR：破壞性變更。
