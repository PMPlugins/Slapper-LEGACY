<?php
namespace slapper\entities\other;

use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use slapper\entities\SlapperEntity;

class SlapperFallingSand extends SlapperEntity
{

    public $entityId = 66;

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
            23 => [7, -1],
            24 => [0, 0]
        ];
        if (isset($this->namedtag->BlockID)) {
            $pk->metadata[20] = [2, $this->namedtag->BlockID->getValue()];
        } else {
            $pk->metadata[20] = [2, 1];
        }
        $player->dataPacket($pk);
        if($this->getDataProperty(3) === 1){
            $this->addNametag($this->getDisplayName($player), $player);
        }        
    }


}