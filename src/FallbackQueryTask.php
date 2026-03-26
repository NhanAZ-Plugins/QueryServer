<?php

declare(strict_types=1);

namespace NhanAZ\QueryServer;

use pocketmine\scheduler\AsyncTask;

class FallbackQueryTask extends AsyncTask {

	public function __construct(
		private string $host,
		private int $port,
		private int $timeout = 2,
		private ?int $requestId = null
	) {
	}

	public function onRun(): void {
		$result = [
			"ok" => false,
			"host" => $this->host,
			"port" => $this->port,
			"data" => null,
			"error" => null,
			"requestId" => $this->requestId
		];

		try {
			$query = \NhanAZ\QueryServer\libpmquery\PMQuery::query($this->host, $this->port, $this->timeout);
			$result["ok"] = true;
			$result["data"] = $query;
		} catch (\Throwable $e) {
			$result["error"] = $e->getMessage();
		}

		$this->setResult($result);
	}

	public function onCompletion(): void {
		Main::handleFallbackResult($this->getResult());
	}
}
