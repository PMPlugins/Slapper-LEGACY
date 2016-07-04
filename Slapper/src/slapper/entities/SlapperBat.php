<?php
namespace slapper\entities;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use slapper\entities\SlapperEntity;

class SlapperBat extends SlapperEntity
{

    public $entityId = 19;

    public function spawnTo(Player $player)
    {

        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = $this->entityId;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->yaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->metadata = [
            15 => [0, 1],
            16 => [0, 0],
            23 => [7, -1],
            24 => [0, 0]
        ];
        $player->dataPacket($pk);
        if($this->getDataProperty(3) === 1){
            $this->addNametag($this->getDisplayName($player), $player);
        }        
    }


}
