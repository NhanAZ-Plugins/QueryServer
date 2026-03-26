<?php

declare(strict_types=1);

namespace Example\QueryConsumer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase {

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if ($command->getName() !== "qtest") {
			return false;
		}

		$queryServer = $this->getServer()->getPluginManager()->getPlugin("QueryServer");
		if (!is_object($queryServer) || !method_exists($queryServer, "getApi")) {
			$sender->sendMessage("QueryServer plugin is not loaded.");
			return true;
		}

		$api = $queryServer->getApi();
		$sender->sendMessage("Querying test.pmmp.io:19132 via QueryServer...");

		$api->query("test.pmmp.io", 19132, function (array $result) use ($sender): void {
			if (($result["ok"] ?? false) !== true) {
				$sender->sendMessage("Query failed: " . ($result["error"] ?? "unknown error"));
				return;
			}

			// $result['source'] === 'api' or 'udp'
			$sender->sendMessage("Source: " . ($result["source"] ?? "unknown"));
			if (isset($result["data"]["HostName"])) {
				// UDP result array
				$sender->sendMessage("HostName: " . $result["data"]["HostName"]);
				$sender->sendMessage("Players: " . $result["data"]["Players"] . "/" . $result["data"]["MaxPlayers"]);
			} elseif (is_object($result["data"] ?? null) && isset($result["data"]->motd?->clean)) {
				// API object
				$sender->sendMessage("MOTD: " . ($result["data"]->motd->clean ?? "n/a"));
				$sender->sendMessage("Online: " . (($result["data"]->players->online ?? 0) . "/" . ($result["data"]->players->max ?? 0)));
			} else {
				$sender->sendMessage("Result payload received.");
			}
		});

		return true;
	}
}
