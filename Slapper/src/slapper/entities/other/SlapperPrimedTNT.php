<?php
namespace slapper\entities\other;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use slapper\entities\SlapperEntity;


class SlapperPrimedTNT extends SlapperEntity
{

    public $entityId = 65;

    public $height = 1.1;

    public function spawnTo(Player $player)
    {

        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = $this->entityId;
        $pk->x = $this->x;
        $pk->y = $this->y + 0.5;
        $pk->z = $this->z;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->metadata = [
            2 => [4, str_ireplace("{name}", $player->getName(), str_ireplace("{display_name}", $player->getDisplayName(), $player->hasPermission("slapper.seeId") ? $this->getDataProperty(2) . "\n" . \pocketmine\utils\TextFormat::GREEN . "Entity ID: " . $this->getId() : $this->getDataProperty(2)))],
            3 => [0, $this->getDataProperty(3)],
            15 => [0, 1],
            23 => [7, -1],
            24 => [0, 0]
        ];
        $player->dataPacket($pk);
        parent::spawnTo($player);
    }


}