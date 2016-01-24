<?php

namespace slapper;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\MobArmorEquipmentPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\protocol\RemovePlayerPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;

class main extends PluginBase implements Listener
{
    public $entityNames = [
        "Human" => ["Human", "Player", "FakePlayer", "Statue", "Person", "Guy", "Dude", 100 => 63],
        "Zombie" => ["Zombie", "Zoby", "Zobmie", 100 => 32],
        "Creeper" => ["Creeper", 100 => 33],
        "Skeleton" => ["Skeleton", "Skelly", "Skellie", 100 => 34],
        "Spider" => ["Spider", 100 => 35],
        "PigZombie" => ["PigZombie", "ZombiePig", "ZombiePigman", "ZobmiePigman", 100 => 36],
        "Slime" => ["Slime", "Smile", "Jelly", "Jiggles", 100 => 37],
        "Enderman" => ["Enderman", "Endy", "Endman", 100 => 38],
        "Silverfish" => ["Silverfish", "Sliverfish", 100 => 39],
        "CaveSpider" => ["CaveSpider", "BlueSpider", "CavernSpider", 100 => 40],
        "Ghast" => ["Ghast", "Ghost", 100 => 41],
        "LavaSlime" => ["LavaSlime", "LavaSmile", "MagmaCube", 100 => 42],
        "Blaze" => ["Blaze", 100 => 43],
        "ZombieVillager" => ["ZombieVillager", "ZobmieVillager", "VillagerZombie", "VillagerZobmie", 100 => 44],
        "Chicken" => ["Chicken", "Chick", 100 => 10],
        "Cow" => ["Cow", "Calf", 100 => 11],
        "Pig" => ["Pig", "Piglet", 100 => 12],
        "Sheep" => ["Sheep", "Lamb", 100 => 13],
        "Wolf" => ["Wolf", "Dog", 100 => 14],
        "Mooshroom" => ["Mooshroom", "MushroomCow", 100 => 16],
        "Squid" => ["Squid", "Octopus", 100 => 17],
        "Rabbit" => ["Rabbit", "Bunny", 100 => 18],
        "Bat" => ["Bat", 100 => 19],
        "IronGolem" => ["IronGolem", "VillagerGolem", 100 => 20],
        "SnowGolem" => ["SnowGolem", "SnowMan", 100 => 21],
        "Ocelot" => ["Ocelot", "Ozelot", "Cat", "Kitty", 100 => 22],
        "Villager" => ["Villager", "Testificate", 100 => 15],
        "PrimedTNT" => ["PrimedTNT", "TNT", "LitTNT", 100 => 65],
        "FallingSand" => ["FallingSand", "FallingBlock", "FakeBlock", 100 => 66],
        "Minecart" => ["Minecart", "Minecraft", 100 => 84],
        "Fireball" => ["Fireball", "GhastFireball", "BigFireball", "NormalFireball", 100 => 85],
        "Boat" => ["Boat", "Bote", 100 => 90]
    ];
    public $prefix = TextFormat::GREEN . "[" . TextFormat::YELLOW . "Slapper" . TextFormat::GREEN . "] ";
    public $entityCount = 0;
    public $entities = [];
    public $entityFile = null;
    public $entityFilePath = "";
    public $spawnedEntities = [];

    public function onEnable()
    {
        $server = $this->getServer();
        $pluginManager = $server->getPluginManager();
        $pluginManager->registerEvents($this, $this);
        $this->saveResource("entities.yml");
        $this->entityFilePath = $this->getDataFolder() . "entities.yml";
        $file = file_get_contents($this->entityFilePath);
        if (empty($file)) {
            $this->entityFile = null;
        } else {
            try {
                $this->entityFile = new Config($this->entityFilePath, Config::YAML);
            } catch (\Exception $e) {
                $this->getLogger()->critical("Slapper config error: \n" . TextFormat::BLUE . $e->getMessage());
                $pluginManager->disablePlugin($this);
                return;
            }
            foreach ($this->entityFile->getAll() as $entity) {
                if (is_array($entity)) {
                    $this->entities[$entity["world"]] = $entity;
                } else {
                    $parsed_entity = yaml_parse($entity);
                    $this->entities[$parsed_entity["world"]] = $parsed_entity;
                }
            }
            $this->entityCount = count($this->entities);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        switch ($command->getName()) {
            case "slapper":
                if (isset($args[0])) {
                    switch ($args[0]) {
                        case "create":
                        case "apawn":
                        case "spawn":
                            if (!$sender instanceof Player) {
                                $sender->sendMessage($this->prefix . "Please run this command ingame.");
                                return true;
                            }
                            if (isset($args[1])) {
                                foreach ($this->entityNames as $nicknames) {
                                    foreach ($nicknames as $nickname) {
                                        if (strtolower($args[1]) === strtolower($nickname)) {
                                            $type = $nicknames[100];
                                            $data = ["name" => isset($args[2]) ? $args[2] : $sender->getNameTag(), "x" => round($sender->getX(), 1), "y" => round($sender->getY(), 1), "z" => round($sender->getZ(), 1), "world" => $sender->getLevel()->getName(), "yaw" => round($sender->getYaw(), 1), "pitch" => round($sender->getPitch(), 1)];
                                            if ($type === 63) {
                                                $data["skinName"] = $sender->getSkinName();
                                                $data["skinData"] = $sender->getSkinData();
                                                $inventory = $sender->getInventory();
                                                $armor = $inventory->getArmorContents();
                                                $item = $inventory->getItemInHand();
                                                $data["item"] = $item->getId();
                                                $data["itemMeta"] = $item->getDamage();
                                                $data["helmet"] = $armor[0]->getId();
                                                $data["chestplate"] = $armor[1]->getId();
                                                $data["leggings"] = $armor[2]->getId();
                                                $data["boots"] = $armor[3]->getId();
                                            }
                                            $this->spawnSlapperToPlayer($this->addToFile($type, $data), $sender);
                                            $sender->sendMessage($this->prefix . "Entity created.");
                                            return true;
                                        }
                                    }
                                }
                                $sender->sendMessage($this->prefix . "Unknown entity type.");
                            } else {
                                $sender->sendMessage($this->prefix . "Please enter entity type.");
                            }
                            return true;
                        case "help":
                            $sender->sendMessage($this->prefix . "Type '/slapper spawn <type> [name]' to spawn an NPC.");
                    }
                } else {
                    $sender->sendMessage($this->prefix . "Type '/slapper help' for help.");
                }
                return true;
        }
        return true;
    }

    public function spawnSlapperToPlayer($id, Player $player)
    {
        foreach ($this->entities as $entity) {
            if ($entity["eid"] === $id) {
                if ($entity["type"] === 63) {
                    $skinData = zlib_decode(base64_decode($entity["skinData"]));
                    if (isset($this->spawnedEntities[$player->getName()])) {
                        $this->spawnedEntities[$player->getName()][] = ["type" => $entity["type"], "id" => $id, "world" => $entity["world"], "uuid" => UUID::fromData($id, $skinData, $entity["name"])];
                    } else {
                        $this->spawnedEntities[$player->getName()] = [["type" => $entity["type"], "id" => $id, "world" => $entity["world"], "uuid" => UUID::fromData($id, $skinData, $entity["name"])]];
                    }
                    $this->spawnHumanToPlayer($player, 10000 + $id, $entity["x"], $entity[1], $entity["z"], $entity["yaw"], $entity["pitch"], $entity["name"], $entity["skinName"], $skinData, $entity["menuName"], $entity["item"], $entity["itemMeta"], [$entity["helmet"], $entity["chestplate"], $entity["leggings"], $entity["boots"]]);
                }
            }
        }
    }

    public function spawnHumanToPlayer(Player $player, $id, $x, $y, $z, $yaw, $pitch, $name, $skinName, $skinData, $menuName = "", $item = 1, $meta = 0, $armor = [])
    {
        $uuid = UUID::fromData($id, $skinData, $name);
        $pk = new AddPlayerPacket();
        $pk->uuid = $uuid;
        $pk->username = "";
        $pk->eid = $id;
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->yaw = $yaw;
        $pk->pitch = $pitch;
        $pk->metadata = [
            2 => [4, $name],
            3 => [0, 1]
        ];
        $pk->item = Item::get($item, $meta, 1);
        $pk->meta = 0;
        $player->dataPacket($pk);

        $add = new PlayerListPacket();
        $add->type = 0;
        $add->entries[] = [$uuid, $id, $menuName, $skinName, $skinData];
        $player->dataPacket($add);

        if ($menuName === "") {
            $remove = new PlayerListPacket();
            $remove->type = 1;
            $remove->entries[] = [$uuid];
            $player->dataPacket($remove);
        }

        if ($armor !== []) {
            $armorPk = new MobArmorEquipmentPacket();
            $armorPk->eid = $id;
            $armorPk->slots = [Item::get($armor[0]), Item::get($armor[1]), Item::get($armor[2]), Item::get($armor[3])];
            $player->dataPacket($armorPk);
        }
    }

    public function addToFile($type, $data)
    {
        $id = 1 + $this->entityCount;
        $entityData = "eid: " . $id . "\ntype: " . $type . "\nname: " . $data["name"] . "\ncommands: []" . "\nx: " . $data["x"] . "\ny: " . $data["y"] . "\nz: " . $data["z"] . "\nworld: " . $data["world"] . "\nyaw: " . $data["yaw"] . "\npitch: " . $data["pitch"];
        switch ($type) {
            case 63:
                $entityData .= "\nmenuName: " . "\"\"" . "\nitem: " . $data["item"] . "\nitemMeta: " . $data["itemMeta"] . "\nhelmet: " . $data["helmet"] . "\nchestplate: " . $data["chestplate"] . "\nleggings: " . $data["leggings"] . "\nboots: " . $data["boots"] . "\nskinName: " . $data["skinName"] . "\nskinData: " . "\"" . base64_encode(zlib_encode($data["skinData"], ZLIB_ENCODING_DEFLATE)) . "\"";
                break;
            case 37:
            case 42:
                $entityData .= "\nsize: 1";
                break;
            case 66:
                $entityData .= "\nblock: 1";
                break;
        }
        if ($this->entityFile instanceof Config) {
            $this->entityFile->set(++$this->entityCount, $entityData);
            $this->entityFile->save();
        } else {
            file_put_contents($this->entityFilePath, ++$this->entityCount . ":\n  " . str_replace("\n", "\n  ", $entityData));
            $this->entityFile = new Config($this->entityFilePath, Config::YAML);
        }
        $this->entities[] = yaml_parse($entityData);
        return $id;
    }

    public function onPlayerRespawn(PlayerRespawnEvent $ev)
    {
        $this->spawnEntitiesToPlayer($ev->getPlayer());
    }

    public function spawnEntitiesToPlayer(Player $player)
    {
        $num = 1;
        $name = $player->getName();
        if(isset($this->spawnedEntities[$name])) {
            while ($num < count($this->spawnedEntities[$name])) {
                $spawnedEntity = $this->spawnedEntities[$name][$num];
                if ($spawnedEntity["type"] === 63) {
                    $this->despawnHumanFromPlayer($player, 10000 + $spawnedEntity["id"], $spawnedEntity["uuid"]);
                }
                unset($this->spawnedEntities[$name][$num]);
                $num++;
            }
        }
        foreach ($this->entities as $entity) {
            if($entity["world"] === $player->getLevel()->getName()) {
                if (isset($entity["type"])) {
                    if ($entity["type"] === 63) {
                        $this->spawnHumanToPlayer($player, 10000 + $entity["eid"], $entity["x"], $entity[1], $entity["z"], $entity["yaw"], $entity["pitch"], $entity["name"], $entity["skinName"], zlib_decode(base64_decode($entity["skinData"])), $entity["menuName"], $entity["item"], $entity["itemMeta"], [$entity["helmet"], $entity["chestplate"], $entity["leggings"], $entity["boots"]]);
                    } else {
                        $this->spawnEntityToPlayer($player, $entity["type"], $entity["x"], $entity[1], $entity["z"], $entity["yaw"], $entity["pitch"]);
                    }
                }
            }
        }
    }

    public function despawnHumanFromPlayer(Player $player, $id, $uuid)
    {
        $pk = new RemovePlayerPacket();
        $pk->eid = $id;
        $pk->clientId = $uuid;
        $player->dataPacket($pk);
    }

    public function spawnEntityToPlayer(Player $player, $type, $x, $y, $z, $yaw, $pitch, array $moreData = [])
    {
        $pk = new AddEntityPacket();
        $pk->eid = 100000 + $this->entityCount;
        $pk->type = $type;
        $pk->x = $x;
        $pk->y = $y;
        $pk->z = $z;
        $pk->yaw = $yaw;
        $pk->pitch = $pitch;
        $pk->metadata = [];
        $player->dataPacket($pk);
    }

    public function despawnEntityFromPlayer(Player $player, $id)
    {
        $pk = new RemoveEntityPacket();
        $pk->eid = $id;
        $player->dataPacket($pk);
    }

    public function onLevelChange(EntityLevelChangeEvent $ev)
    {
        if($ev->isCancelled()) return;
        $entity = $ev->getEntity();
        if($entity instanceof Player) {
            foreach($this->entities[$ev->getOrigin()->getName()] as $oldEntity){
                if($oldEntity["type"] === 63){
                    $this->despawnHumanFromPlayer($entity, 10000 + $oldEntity["id"], $oldEntity["uuid"]);
                } else {
                    $this->despawnEntityFromPlayer($entity, 10000 + $oldEntity["id"]);
                }
            }
            foreach($this->entities[$ev->getTarget()->getName()] as $newEntity){
                if($newEntity["type"] === 63){
                    $this->spawnHumanToPlayer($entity, 10000 + $newEntity["eid"], $newEntity["x"], $newEntity[1], $newEntity["z"], $newEntity["yaw"], $newEntity["pitch"], $newEntity["name"], $newEntity["skinName"], zlib_decode(base64_decode($newEntity["skinData"])), $newEntity["menuName"], $newEntity["item"], $newEntity["itemMeta"], [$newEntity["helmet"], $newEntity["chestplate"], $newEntity["leggings"], $newEntity["boots"]]);
                } else {
                    $this->spawnEntityToPlayer($entity, $newEntity["type"], $newEntity["x"], $newEntity[1], $newEntity["z"], $newEntity["yaw"], $newEntity["pitch"]);
                }
            }
        }
    }
}