<?php

declare(strict_types=1);

namespace DevicePrefix;

use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;

class DevicePrefix extends PluginBase implements Listener {

	private $playerDevices; // Usage: $playerDevices['Steve']['DeviceNumber'] OR $playerDevices['Alex']['DeviceName']

	private function getDeviceNameByNumber(int $id) {
		static $deviceNames = array(1 => 'Android', 'iOS', 'Mac', 'FireOS', 'GearVR', 'HoloLens', 'Win10', 'Windows', 'Dedicated', 'tvOS', 'PS4', 'Switch', 'Xbox'); // Hope the list is all right
		return $deviceNames[$id] ?? 'unknown';
	}

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onDisable() {
		unset($this->playerDevices);
	}

	public function DataPacketReceive(DataPacketReceiveEvent $event) {
		$packet = $event->getPacket();
		if($packet instanceof LoginPacket) {
			$this->playerDevices[$packet->username]['DeviceNumber'] = $packet->clientData['DeviceOS'];
			$this->playerDevices[$packet->username]['DeviceName'] = $this->getDeviceNameByNumber($this->playerDevices[$packet->username]['DeviceNumber']);
		}
	}

	public function PlayerJoin(PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		$playerName = $player->getName();
		$this->getServer()->broadcastMessage(sprintf(
			'§a%s joined the server with a §e%s§a device.', // Join message format
			$playerName, $this->playerDevices[$playerName]['DeviceName']
		));
		$player->setDisplayName(sprintf(
			$this->playerDevices[$playerName]['DeviceName'], $playerName
			'§a[%s§a] §r%s', // Better
		));
	}

}
