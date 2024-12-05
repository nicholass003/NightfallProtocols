<?php

declare(strict_types=1);

namespace Supero\NightfallProtocol\network\packets;

use pocketmine\network\mcpe\protocol\ResourcePacksInfoPacket as PM_Packet;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Supero\NightfallProtocol\network\CustomProtocolInfo;
use Supero\NightfallProtocol\network\packets\types\resourcepacks\CustomBehaviourPackInfoEntry;
use Supero\NightfallProtocol\network\packets\types\resourcepacks\CustomResourcePackInfoEntry;
use Supero\NightfallProtocol\utils\ReflectionUtils;

use function count;

class ResourcePacksInfoPacket extends PM_Packet
{
	/** @var CustomResourcePackInfoEntry[] */
	public array $resourcePackEntries = [];
	/** @var CustomBehaviourPackInfoEntry[] */
	public array $behaviorPackEntries = [];
	public bool $mustAccept = false; //if true, forces client to choose between accepting packs or being disconnected
	public bool $hasAddons = false;
	public bool $hasScripts = false; //if true, causes disconnect for any platform that doesn't support scripts yet
	public bool $forceServerPacks = false;
	public UuidInterface $worldTemplateId;
	public string $worldTemplateVersion;
	/**
	 * @var string[]
	 * @phpstan-var array<string, string>
	 */
	public array $cdnUrls = [];

	/**
	 * @generate-create-func
	 * @param CustomResourcePackInfoEntry[]  $resourcePackEntries
	 * @param CustomBehaviourPackInfoEntry[] $behaviorPackEntries
	 * @param string[]                       $cdnUrls
	 * @phpstan-param array<string, string> $cdnUrls
	 */
	public static function createPacket(
		array $resourcePackEntries,
		array $behaviorPackEntries,
		bool $mustAccept,
		bool $hasAddons,
		bool $hasScripts,
		UuidInterface $worldTemplateId,
		string $worldTemplateVersion,
		bool $forceServerPacks,
		array $cdnUrls,
	) : self{
		$result = new self();
		$result->resourcePackEntries = $resourcePackEntries;
		$result->behaviorPackEntries = $behaviorPackEntries;
		$result->mustAccept = $mustAccept;
		$result->hasAddons = $hasAddons;
		$result->hasScripts = $hasScripts;
		$result->worldTemplateId = $worldTemplateId;
		$result->worldTemplateVersion = $worldTemplateVersion;
		$result->forceServerPacks = $forceServerPacks;
		$result->cdnUrls = $cdnUrls;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->mustAccept = $in->getBool();
		if($in->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_20_70){
			$this->hasAddons = $in->getBool();
		}
		$this->hasScripts = $in->getBool();
		if($in->getProtocol() <= CustomProtocolInfo::PROTOCOL_1_21_20){
			$this->forceServerPacks = $in->getBool();
			$behaviorPackCount = $in->getLShort();
			while($behaviorPackCount-- > 0){
				$this->behaviorPackEntries[] = CustomBehaviourPackInfoEntry::read($in);
			}
		}
		if ($in->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_21_50) {
			$this->worldTemplateId = $in->getUUID();
			$this->worldTemplateVersion = $in->getString();
		}

		$resourcePackCount = $in->getLShort();
		while($resourcePackCount-- > 0){
			$this->resourcePackEntries[] = CustomResourcePackInfoEntry::read($in);
		}

		if($in->getProtocol() < CustomProtocolInfo::PROTOCOL_1_21_40){
			$this->cdnUrls = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; $i++){
				$packId = $in->getString();
				$cdnUrl = $in->getString();
				$this->cdnUrls[$packId] = $cdnUrl;
			}
		}
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putBool($this->mustAccept);
		if($out->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_20_70) {
			$out->putBool($this->hasAddons);
		}
		$out->putBool($this->hasScripts);
		if($out->getProtocol() <= CustomProtocolInfo::PROTOCOL_1_21_20) {
			$out->putBool($this->forceServerPacks);
			$out->putLShort(count($this->behaviorPackEntries));
			foreach ($this->behaviorPackEntries as $entry) {
				$entry->write($out);
			}
		}
		if ($out->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_21_50) {
			$out->putUUID($this->worldTemplateId);
			$out->putString($this->worldTemplateVersion);
		}
		$out->putLShort(count($this->resourcePackEntries));
		foreach($this->resourcePackEntries as $entry){
			$entry->write($out);
		}
		if($out->getProtocol() < CustomProtocolInfo::PROTOCOL_1_21_40){
			$out->putUnsignedVarInt(count($this->cdnUrls));
			foreach($this->cdnUrls as $packId => $cdnUrl){
				$out->putString($packId);
				$out->putString($cdnUrl);
			}
		}
	}

	public function getConstructorArguments(PM_Packet $packet) : array
	{
		return [
			$packet->resourcePackEntries,
			[],
			$packet->mustAccept,
			$packet->hasAddons ?? false,
			$packet->hasScripts,
			ReflectionUtils::getProperty($packet::class, $packet, "worldTemplateId"),
			ReflectionUtils::getProperty($packet::class, $packet, "worldTemplateVersion"),
			false,
			[]
		];
	}
}
