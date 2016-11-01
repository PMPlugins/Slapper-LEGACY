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
            self::DATA_FLAGS => [self::DATA_TYPE_LONG, (1 << self::DATA_FLAG_NO_AI)],
            self::DATA_LEAD_HOLDER_EID => [self::DATA_TYPE_LONG, -1]
        ];
        if (isset($this->namedtag->BlockID)) {
            $pk->metadata[self::DATA_VARIANT] = [self::DATA_TYPE_INT, $this->namedtag->BlockID->getValue()];
        } else {
            $pk->metadata[self::DATA_VARIANT] = [self::DATA_TYPE_INT, 1];
        }
        $player->dataPacket($pk);
        if($this->isNameTagVisible()){
            $this->addNametag($this->getDisplayName($player), $player);
        }        
    }


}