<?php

namespace Supero\NightfallProtocol\network\packets\types\inventory\stackrequest;

use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequestAction;

final class PlaceIntoBundleStackRequestAction extends ItemStackRequestAction{
    use GetTypeIdFromConstTrait;
    use TakeOrPlaceStackRequestActionTrait;

    public const ID = 7;
}