<?php

namespace Supero\NightfallProtocol\network\packets\types;

use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\ChatRestrictionLevel;
use pocketmine\network\mcpe\protocol\types\EditorWorldType;
use pocketmine\network\mcpe\protocol\types\EducationEditionOffer;
use pocketmine\network\mcpe\protocol\types\EducationUriResource;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\network\mcpe\protocol\types\GameRule;
use pocketmine\network\mcpe\protocol\types\GeneratorType;
use pocketmine\network\mcpe\protocol\types\LevelSettings;
use pocketmine\network\mcpe\protocol\types\MultiplayerGameVisibility;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\types\SpawnSettings;
use pocketmine\utils\BinaryDataException;
use Supero\NightfallProtocol\network\CustomProtocolInfo;

class CustomLevelSettings
{
    public int $seed;
    public SpawnSettings $spawnSettings;
    public int $generator = GeneratorType::OVERWORLD;
    public int $worldGamemode;
    public bool $hardcore = false;
    public int $difficulty;
    public BlockPosition $spawnPosition;
    public bool $hasAchievementsDisabled = true;
    public int $editorWorldType = EditorWorldType::NON_EDITOR;
    public bool $createdInEditorMode = false;
    public bool $exportedFromEditorMode = false;
    public int $time = -1;
    public int $eduEditionOffer = EducationEditionOffer::NONE;
    public bool $hasEduFeaturesEnabled = false;
    public string $eduProductUUID = "";
    public float $rainLevel;
    public float $lightningLevel;
    public bool $hasConfirmedPlatformLockedContent = false;
    public bool $isMultiplayerGame = true;
    public bool $hasLANBroadcast = true;
    public int $xboxLiveBroadcastMode = MultiplayerGameVisibility::PUBLIC;
    public int $platformBroadcastMode = MultiplayerGameVisibility::PUBLIC;
    public bool $commandsEnabled;
    public bool $isTexturePacksRequired = true;
    /**
     * @var GameRule[]
     * @phpstan-var array<string, GameRule>
     */
    public array $gameRules = [];
    public Experiments $experiments;
    public bool $hasBonusChestEnabled = false;
    public bool $hasStartWithMapEnabled = false;
    public int $defaultPlayerPermission = PlayerPermissions::MEMBER;

    public int $serverChunkTickRadius = 4;

    public bool $hasLockedBehaviorPack = false;
    public bool $hasLockedResourcePack = false;
    public bool $isFromLockedWorldTemplate = false;
    public bool $useMsaGamertagsOnly = false;
    public bool $isFromWorldTemplate = false;
    public bool $isWorldTemplateOptionLocked = false;
    public bool $onlySpawnV1Villagers = false;
    public bool $disablePersona = false;
    public bool $disableCustomSkins = false;
    public bool $muteEmoteAnnouncements = false;
    public string $vanillaVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
    public int $limitedWorldWidth = 0;
    public int $limitedWorldLength = 0;
    public bool $isNewNether = true;
    public ?EducationUriResource $eduSharedUriResource = null;
    public ?bool $experimentalGameplayOverride = null;
    public int $chatRestrictionLevel = ChatRestrictionLevel::NONE;
    public bool $disablePlayerInteractions = false;

    public string $serverIdentifier = "";
    public string $worldIdentifier = "";
    public string $scenarioIdentifier = "";

    /**
     * @throws BinaryDataException
     * @throws PacketDecodeException
     */
    public static function read(PacketSerializer $in) : self{
        $result = new self;
        $result->internalRead($in);
        return $result;
    }

    public static function convert(self $settings) : LevelSettings{
        $result = new LevelSettings();
        $result->seed = $settings->seed;
        $result->spawnSettings = $settings->spawnSettings;
        $result->generator = $settings->generator;
        $result->worldGamemode = $settings->worldGamemode;
        $result->hardcore = $settings->hardcore;
        $result->difficulty = $settings->difficulty;
        $result->spawnPosition = $settings->spawnPosition;
        $result->hasAchievementsDisabled = $settings->hasAchievementsDisabled;
        $result->editorWorldType = $settings->editorWorldType;
        $result->createdInEditorMode = $settings->createdInEditorMode;
        $result->exportedFromEditorMode = $settings->exportedFromEditorMode;
        $result->time = $settings->time;
        $result->eduEditionOffer = $settings->eduEditionOffer;
        $result->hasEduFeaturesEnabled = $settings->hasEduFeaturesEnabled;
        $result->eduProductUUID = $settings->eduProductUUID;
        $result->rainLevel = $settings->rainLevel;
        $result->lightningLevel = $settings->lightningLevel;
        $result->hasConfirmedPlatformLockedContent = $settings->hasConfirmedPlatformLockedContent;
        $result->isMultiplayerGame = $settings->isMultiplayerGame;
        $result->hasLANBroadcast = $settings->hasLANBroadcast;
        $result->xboxLiveBroadcastMode = $settings->xboxLiveBroadcastMode;
        $result->platformBroadcastMode = $settings->platformBroadcastMode;
        $result->commandsEnabled = $settings->commandsEnabled;
        $result->isTexturePacksRequired = $settings->isTexturePacksRequired;
        $result->gameRules = $settings->gameRules;
        $result->experiments = $settings->experiments;
        $result->hasBonusChestEnabled = $settings->hasBonusChestEnabled;
        $result->hasStartWithMapEnabled = $settings->hasStartWithMapEnabled;
        $result->defaultPlayerPermission = $settings->defaultPlayerPermission;
        $result->serverChunkTickRadius = $settings->serverChunkTickRadius;
        $result->hasLockedBehaviorPack = $settings->hasLockedBehaviorPack;
        $result->hasLockedResourcePack = $settings->hasLockedResourcePack;
        $result->isFromLockedWorldTemplate = $settings->isFromLockedWorldTemplate;
        $result->useMsaGamertagsOnly = $settings->useMsaGamertagsOnly;
        $result->isFromWorldTemplate = $settings->isFromWorldTemplate;
        $result->isWorldTemplateOptionLocked = $settings->isWorldTemplateOptionLocked;
        $result->onlySpawnV1Villagers = $settings->onlySpawnV1Villagers;
        $result->disablePersona = $settings->disablePersona;
        $result->disableCustomSkins = $settings->disableCustomSkins;
        $result->muteEmoteAnnouncements = $settings->muteEmoteAnnouncements;
        $result->vanillaVersion = $settings->vanillaVersion;
        $result->limitedWorldWidth = $settings->limitedWorldWidth;
        $result->limitedWorldLength = $settings->limitedWorldLength;
        $result->isNewNether = $settings->isNewNether;
        $result->eduSharedUriResource = $settings->eduSharedUriResource;
        $result->experimentalGameplayOverride = $settings->experimentalGameplayOverride;
        $result->chatRestrictionLevel = $settings->chatRestrictionLevel;
        $result->disablePlayerInteractions = $settings->disablePlayerInteractions;
        $result->serverIdentifier = $settings->serverIdentifier;
        $result->worldIdentifier = $settings->worldIdentifier;
        $result->scenarioIdentifier = $settings->scenarioIdentifier;
        return $result;
    }    

    /**
     * @throws BinaryDataException
     * @throws PacketDecodeException
     */
    private function internalRead(PacketSerializer $in) : void{
        $this->seed = $in->getLLong();
        $this->spawnSettings = SpawnSettings::read($in);
        $this->generator = $in->getVarInt();
        $this->worldGamemode = $in->getVarInt();
        if($in->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_20_80) {
            $this->hardcore = $in->getBool();
        }
        $this->difficulty = $in->getVarInt();
        $this->spawnPosition = $in->getBlockPosition();
        $this->hasAchievementsDisabled = $in->getBool();
        $this->editorWorldType = $in->getVarInt();
        $this->createdInEditorMode = $in->getBool();
        $this->exportedFromEditorMode = $in->getBool();
        $this->time = $in->getVarInt();
        $this->eduEditionOffer = $in->getVarInt();
        $this->hasEduFeaturesEnabled = $in->getBool();
        $this->eduProductUUID = $in->getString();
        $this->rainLevel = $in->getLFloat();
        $this->lightningLevel = $in->getLFloat();
        $this->hasConfirmedPlatformLockedContent = $in->getBool();
        $this->isMultiplayerGame = $in->getBool();
        $this->hasLANBroadcast = $in->getBool();
        $this->xboxLiveBroadcastMode = $in->getVarInt();
        $this->platformBroadcastMode = $in->getVarInt();
        $this->commandsEnabled = $in->getBool();
        $this->isTexturePacksRequired = $in->getBool();
        $this->gameRules = $in->getGameRules();
        $this->experiments = Experiments::read($in);
        $this->hasBonusChestEnabled = $in->getBool();
        $this->hasStartWithMapEnabled = $in->getBool();
        $this->defaultPlayerPermission = $in->getVarInt();
        $this->serverChunkTickRadius = $in->getLInt();
        $this->hasLockedBehaviorPack = $in->getBool();
        $this->hasLockedResourcePack = $in->getBool();
        $this->isFromLockedWorldTemplate = $in->getBool();
        $this->useMsaGamertagsOnly = $in->getBool();
        $this->isFromWorldTemplate = $in->getBool();
        $this->isWorldTemplateOptionLocked = $in->getBool();
        $this->onlySpawnV1Villagers = $in->getBool();
        $this->disablePersona = $in->getBool();
        $this->disableCustomSkins = $in->getBool();
        $this->muteEmoteAnnouncements = $in->getBool();
        $this->vanillaVersion = $in->getString();
        $this->limitedWorldWidth = $in->getLInt();
        $this->limitedWorldLength = $in->getLInt();
        $this->isNewNether = $in->getBool();
        $this->eduSharedUriResource = EducationUriResource::read($in);
        $this->experimentalGameplayOverride = $in->readOptional($in->getBool(...));
        $this->chatRestrictionLevel = $in->getByte();
        $this->disablePlayerInteractions = $in->getBool();
        if($in->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_21_0){
            $this->serverIdentifier = $in->getString();
            $this->worldIdentifier = $in->getString();
            $this->scenarioIdentifier = $in->getString();
        }
    }

    public function write(PacketSerializer $out) : void{
        $out->putLLong($this->seed);
        $this->spawnSettings->write($out);
        $out->putVarInt($this->generator);
        $out->putVarInt($this->worldGamemode);
        if($out->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_20_80) {
            $out->putBool($this->hardcore);
        }
        $out->putVarInt($this->difficulty);
        $out->putBlockPosition($this->spawnPosition);
        $out->putBool($this->hasAchievementsDisabled);
        $out->putVarInt($this->editorWorldType);
        $out->putBool($this->createdInEditorMode);
        $out->putBool($this->exportedFromEditorMode);
        $out->putVarInt($this->time);
        $out->putVarInt($this->eduEditionOffer);
        $out->putBool($this->hasEduFeaturesEnabled);
        $out->putString($this->eduProductUUID);
        $out->putLFloat($this->rainLevel);
        $out->putLFloat($this->lightningLevel);
        $out->putBool($this->hasConfirmedPlatformLockedContent);
        $out->putBool($this->isMultiplayerGame);
        $out->putBool($this->hasLANBroadcast);
        $out->putVarInt($this->xboxLiveBroadcastMode);
        $out->putVarInt($this->platformBroadcastMode);
        $out->putBool($this->commandsEnabled);
        $out->putBool($this->isTexturePacksRequired);
        $out->putGameRules($this->gameRules);
        $this->experiments->write($out);
        $out->putBool($this->hasBonusChestEnabled);
        $out->putBool($this->hasStartWithMapEnabled);
        $out->putVarInt($this->defaultPlayerPermission);
        $out->putLInt($this->serverChunkTickRadius);
        $out->putBool($this->hasLockedBehaviorPack);
        $out->putBool($this->hasLockedResourcePack);
        $out->putBool($this->isFromLockedWorldTemplate);
        $out->putBool($this->useMsaGamertagsOnly);
        $out->putBool($this->isFromWorldTemplate);
        $out->putBool($this->isWorldTemplateOptionLocked);
        $out->putBool($this->onlySpawnV1Villagers);
        $out->putBool($this->disablePersona);
        $out->putBool($this->disableCustomSkins);
        $out->putBool($this->muteEmoteAnnouncements);
        $out->putString($this->vanillaVersion);
        $out->putLInt($this->limitedWorldWidth);
        $out->putLInt($this->limitedWorldLength);
        $out->putBool($this->isNewNether);
        ($this->eduSharedUriResource ?? new EducationUriResource("", ""))->write($out);
        $out->writeOptional($this->experimentalGameplayOverride, $out->putBool(...));
        $out->putByte($this->chatRestrictionLevel);
        $out->putBool($this->disablePlayerInteractions);
        if($out->getProtocol() >= CustomProtocolInfo::PROTOCOL_1_21_0){
            $out->putString($this->serverIdentifier);
            $out->putString($this->worldIdentifier);
            $out->putString($this->scenarioIdentifier);
        }
    }
}