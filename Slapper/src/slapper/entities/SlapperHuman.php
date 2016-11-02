<?php
namespace slapper\entities;

use pocketmine\entity\Human;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\Player;

class SlapperHuman extends Human {

    public function spawnTo(Player $player)
    {
        if ($player !== $this and !isset($this->hasSpawned[$player->getLoaderId()])) {
            $this->hasSpawned[$player->getLoaderId()] = $player;

            $uuid = $this->getUniqueId();
            $entityId = $this->getId();

            $pk = new AddPlayerPacket();
            $pk->uuid = $uuid;
            $pk->username = "";
            $pk->eid = $entityId;
            $pk->x = $this->x;
            $pk->y = $this->y;
            $pk->z = $this->z;
            $pk->yaw = $this->yaw;
            $pk->pitch = $this->pitch;
            $pk->item = $this->getInventory()->getItemInHand();
            $pk->metadata = [
                self::DATA_FLAGS => [self::DATA_TYPE_LONG, ((1 << self::DATA_FLAG_NO_AI) | ($this->isNameTagVisible() ? (1 << self::DATA_FLAG_CAN_SHOW_NAMETAG) : 0))],
                self::DATA_NAMETAG => [self::DATA_TYPE_STRING, str_ireplace("{name}", $player->getName(), str_ireplace("{display_name}", $player->getDisplayName(), $player->hasPermission("slapper.seeId") ? $this->getNameTag() . "\n" . \pocketmine\utils\TextFormat::GREEN . "Entity ID: " . $entityId : $this->getNameTag()))],
                self::DATA_LEAD_HOLDER_EID => [self::DATA_TYPE_LONG, -1]
            ];
            $player->dataPacket($pk);

            $this->inventory->sendArmorContents($player);

            $add = new PlayerListPacket();
            $add->type = 0;
            $add->entries[] = [$uuid, $entityId, isset($this->namedtag->MenuName) ? $this->namedtag["MenuName"] : "", $this->skinId, $this->skin];
            $player->dataPacket($add);
            if ($this->namedtag["MenuName"] === "") {
                $remove = new PlayerListPacket();
                $remove->type = 1;
                $remove->entries[] = [$uuid];
                $player->dataPacket($remove);
            }
        }
    }
}
