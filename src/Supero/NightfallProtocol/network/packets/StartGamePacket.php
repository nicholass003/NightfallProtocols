<?php

declare(strict_types=1);

namespace Supero\NightfallProtocol\network\packets;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\StartGamePacket as PM_Packet;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\network\mcpe\protocol\types\NetworkPermissions;
use pocketmine\network\mcpe\protocol\types\PlayerMovementSettings;
use Ramsey\Uuid\UuidInterface;
use Supero\NightfallProtocol\network\CustomProtocolInfo;
use Supero\NightfallProtocol\network\packets\types\CustomLevelSettings;
use function count;

class StartGamePacket extends PM_Packet
{

	public int $actorUniqueId;
	public int $actorRuntimeId;
	public int $playerGamemode;

	public Vector3 $playerPosition;

	public float $pitch;
	public float $yaw;

	/** @phpstan-var CacheableNbt<CompoundTag>  */
	public CacheableNbt $playerActorProperties; //same as SyncActorPropertyPacket content

	public CustomLevelSettings $customLevelSettings;

	public string $levelId = ""; //base64 string, usually the same as world folder name in vanilla
	public string $worldName;
	public string $premiumWorldTemplateId = "";
	public bool $isTrial = false;
	public PlayerMovementSettings $playerMovementSettings;
	public int $currentTick = 0; //only used if isTrial is true
	public int $enchantmentSeed = 0;
	public string $multiplayerCorrelationId = "";
	public bool $enableNewInventorySystem = false;
	public string $serverSoftwareVersion;
	public UuidInterface $worldTemplateId; //why is this here twice ??? mojang
	public bool $enableClientSideChunkGeneration;
	public bool $blockNetworkIdsAreHashes = false; //new in 1.19.80, possibly useful for multi version
	public NetworkPermissions $networkPermissions;

	/**
	 * @var BlockPaletteEntry[]
	 * @phpstan-var list<BlockPaletteEntry>
	 */
	public array $blockPalette = [];

	/**
	 * Checksum of the full block palette. This is a hash of some weird stringified version of the NBT.
	 * This is used along with the baseGameVersion to check for inconsistencies in the block palette.
	 * Fill with 0 if you don't want to bother having the client verify the palette (seems pointless anyway).
	 */
	public int $blockPaletteChecksum;

	/**
	 * @var ItemTypeEntry[]
	 * @phpstan-var list<ItemTypeEntry>
	 */
	public array $itemTable;

	/**
	 * @generate-create-func
	 * @param BlockPaletteEntry[] $blockPalette
	 * @param ItemTypeEntry[]     $itemTable
	 * @phpstan-param CacheableNbt<CompoundTag> $playerActorProperties
	 * @phpstan-param list<BlockPaletteEntry>   $blockPalette
	 * @phpstan-param list<ItemTypeEntry>       $itemTable
	 */
	public static function createPacket(
		int $actorUniqueId,
		int $actorRuntimeId,
		int $playerGamemode,
		Vector3 $playerPosition,
		float $pitch,
		float $yaw,
		CacheableNbt $playerActorProperties,
		CustomLevelSettings $customLevelSettings,
		string $levelId,
		string $worldName,
		string $premiumWorldTemplateId,
		bool $isTrial,
		PlayerMovementSettings $playerMovementSettings,
		int $currentTick,
		int $enchantmentSeed,
		string $multiplayerCorrelationId,
		bool $enableNewInventorySystem,
		string $serverSoftwareVersion,
		UuidInterface $worldTemplateId,
		bool $enableClientSideChunkGeneration,
		bool $blockNetworkIdsAreHashes,
		NetworkPermissions $networkPermissions,
		array $blockPalette,
		int $blockPaletteChecksum,
		array $itemTable,
	) : self{
		$result = new self();
		$result->actorUniqueId = $actorUniqueId;
		$result->actorRuntimeId = $actorRuntimeId;
		$result->playerGamemode = $playerGamemode;
		$result->playerPosition = $playerPosition;
		$result->pitch = $pitch;
		$result->yaw = $yaw;
		$result->playerActorProperties = $playerActorProperties;
		$result->customLevelSettings = $customLevelSettings;
		$result->levelId = $levelId;
		$result->worldName = $worldName;
		$result->premiumWorldTemplateId = $premiumWorldTemplateId;
		$result->isTrial = $isTrial;
		$result->playerMovementSettings = $playerMovementSettings;
		$result->currentTick = $currentTick;
		$result->enchantmentSeed = $enchantmentSeed;
		$result->multiplayerCorrelationId = $multiplayerCorrelationId;
		$result->enableNewInventorySystem = $enableNewInventorySystem;
		$result->serverSoftwareVersion = $serverSoftwareVersion;
		$result->worldTemplateId = $worldTemplateId;
		$result->enableClientSideChunkGeneration = $enableClientSideChunkGeneration;
		$result->blockNetworkIdsAreHashes = $blockNetworkIdsAreHashes;
		$result->networkPermissions = $networkPermissions;
		$result->blockPalette = $blockPalette;
		$result->blockPaletteChecksum = $blockPaletteChecksum;
		$result->itemTable = $itemTable;
		return $result;
	}

	protected function decodePayload(PacketSerializer $in) : void{
		$this->actorUniqueId = $in->getActorUniqueId();
		$this->actorRuntimeId = $in->getActorRuntimeId();
		$this->playerGamemode = $in->getVarInt();

		$this->playerPosition = $in->getVector3();

		$this->pitch = $in->getLFloat();
		$this->yaw = $in->getLFloat();

		$this->customLevelSettings = CustomLevelSettings::read($in);

		$this->levelId = $in->getString();
		$this->worldName = $in->getString();
		$this->premiumWorldTemplateId = $in->getString();
		$this->isTrial = $in->getBool();
		$this->playerMovementSettings = PlayerMovementSettings::read($in);
		$this->currentTick = $in->getLLong();

		$this->enchantmentSeed = $in->getVarInt();

		$this->blockPalette = [];
		for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
			$blockName = $in->getString();
			$state = $in->getNbtCompoundRoot();
			$this->blockPalette[] = new BlockPaletteEntry($blockName, new CacheableNbt($state));
		}

		if($Tin->getProtocol() <= CustomProtocolInfo::PROTOCOL_1_21_50){
			$this->itemTable = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
				$stringId = $in->getString();
				$numericId = $in->getSignedLShort();
				$isComponentBased = $in->getBool();

				$this->itemTable[] = new ItemTypeEntry($stringId, $numericId, $isComponentBased, -1, new CacheableNbt(new CompoundTag()));
			}
		}

		$this->multiplayerCorrelationId = $in->getString();
		$this->enableNewInventorySystem = $in->getBool();
		$this->serverSoftwareVersion = $in->getString();
		$this->playerActorProperties = new CacheableNbt($in->getNbtCompoundRoot());
		$this->blockPaletteChecksum = $in->getLLong();
		$this->worldTemplateId = $in->getUUID();
		$this->enableClientSideChunkGeneration = $in->getBool();
		$this->blockNetworkIdsAreHashes = $in->getBool();
		$this->networkPermissions = NetworkPermissions::decode($in);
	}

	protected function encodePayload(PacketSerializer $out) : void{
		$out->putActorUniqueId($this->actorUniqueId);
		$out->putActorRuntimeId($this->actorRuntimeId);
		$out->putVarInt($this->playerGamemode);

		$out->putVector3($this->playerPosition);

		$out->putLFloat($this->pitch);
		$out->putLFloat($this->yaw);

		$this->customLevelSettings->write($out);

		$out->putString($this->levelId);
		$out->putString($this->worldName);
		$out->putString($this->premiumWorldTemplateId);
		$out->putBool($this->isTrial);
		$this->playerMovementSettings->write($out);
		$out->putLLong($this->currentTick);

		$out->putVarInt($this->enchantmentSeed);

		$out->putUnsignedVarInt(count($this->blockPalette));
		foreach($this->blockPalette as $entry){
			$out->putString($entry->getName());
			$out->put($entry->getStates()->getEncodedNbt());
		}

		if($out->getProtocol() <= CustomProtocolInfo::PROTOCOL_1_21_50){
			$out->putUnsignedVarInt(count($this->itemTable));
			foreach($this->itemTable as $entry){
				$out->putString($entry->getStringId());
				$out->putLShort($entry->getNumericId());
				$out->putBool($entry->isComponentBased());
			}
		}

		$out->putString($this->multiplayerCorrelationId);
		$out->putBool($this->enableNewInventorySystem);
		$out->putString($this->serverSoftwareVersion);
		$out->put($this->playerActorProperties->getEncodedNbt());
		$out->putLLong($this->blockPaletteChecksum);
		$out->putUUID($this->worldTemplateId);
		$out->putBool($this->enableClientSideChunkGeneration);
		$out->putBool($this->blockNetworkIdsAreHashes);
		$this->networkPermissions->encode($out);
	}

	public function getConstructorArguments(PM_Packet $packet) : array {

		$levelSettings = $packet->levelSettings;
		$customLevelSettings = new CustomLevelSettings();

		$customLevelSettings->seed = $packet->levelSettings->seed;
		$customLevelSettings->spawnSettings = $levelSettings->spawnSettings;
		$customLevelSettings->worldGamemode = $levelSettings->worldGamemode;
		$customLevelSettings->difficulty = $levelSettings->difficulty;
		$customLevelSettings->spawnPosition = $levelSettings->spawnPosition;
		$customLevelSettings->hasAchievementsDisabled = $levelSettings->hasAchievementsDisabled;
		$customLevelSettings->time = $levelSettings->time;
		$customLevelSettings->eduEditionOffer = $levelSettings->eduEditionOffer;
		$customLevelSettings->rainLevel = $levelSettings->rainLevel;
		$customLevelSettings->lightningLevel = $levelSettings->lightningLevel;
		$customLevelSettings->commandsEnabled = $levelSettings->commandsEnabled;
		$customLevelSettings->gameRules = $levelSettings->gameRules;
		$customLevelSettings->experiments = $levelSettings->experiments;

		return [
			$packet->actorUniqueId,
			$packet->actorRuntimeId,
			$packet->playerGamemode,
			$packet->playerPosition,
			$packet->pitch,
			$packet->yaw,
			$packet->playerActorProperties,
			$customLevelSettings,
			$packet->levelId,
			$packet->worldName,
			$packet->premiumWorldTemplateId,
			$packet->isTrial,
			$packet->playerMovementSettings,
			$packet->currentTick,
			$packet->enchantmentSeed,
			$packet->multiplayerCorrelationId,
			$packet->enableNewInventorySystem,
			$packet->serverSoftwareVersion,
			$packet->worldTemplateId,
			$packet->enableClientSideChunkGeneration,
			$packet->blockNetworkIdsAreHashes,
			$packet->networkPermissions,
			$packet->blockPalette,
			$packet->blockPaletteChecksum,
			$packet->itemTable ?? []
		];
	}

}
