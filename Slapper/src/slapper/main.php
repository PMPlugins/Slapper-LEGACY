<?php

namespace slapper;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;
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
                $this->entityFile = new Config($this->entityFilePath);
            } catch (\Exception $e) {
                $this->getLogger()->critical("Slapper config error: \n" . TextFormat::BLUE . $e->getMessage());
                $pluginManager->disablePlugin($this);
                return;
            }
            $this->entities = $this->entityFile->getAll();
            $this->entityCount = count($this->entities);
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $isPlayer = $sender instanceof Player;
        switch ($command->getName()) {
            case "slapper":
                var_dump(zlib_decode($sender->namedtag["Skin"]["Data"]));
                if (isset($args[0])) {
                    switch ($args[0]) {
                        case "create":
                        case "spawn":
                            if (!($isPlayer)) {
                                $sender->sendMessage($this->prefix . "Please run this command ingame.");
                                return true;
                            }
                            if (isset($args[1])) {
                                foreach ($this->entityNames as $nicknames) {
                                    foreach ($nicknames as $nickname) {
                                        if (strtolower($args[1]) === strtolower($nickname)) {
                                            $type = $nicknames[100];
                                            if ($type === 63) {
                                                $this->addToFile(isset($args[2]) ? $args[2] : $sender->getNameTag(), $type, round($sender->x, 1), round($sender->y, 1), round($sender->z, 1), $sender->getLevel()->getName(), $sender->yaw, $sender->pitch, [], $sender->getSkinName(), $sender->getSkinData());
                                            } else {
                                                $this->addToFile(isset($args[2]) ? $args[2] : $sender->getNameTag(), $type, round($sender->x, 1), round($sender->y, 1), round($sender->z, 1), $sender->getLevel()->getName(), $sender->yaw, $sender->pitch);
                                            }
                                            $sender->sendMessage($this->prefix . "Entity created."); // will change later
                                            return true;
                                        }
                                    }
                                }
                                //tell player invalid entity
                            } else {
                                // enter type
                            }
                            return true;
                    }
                } else {
                    // more args, I'll get back to this later, focusing on the packets/network stuff for now
                }
                return true;
        }
        return true;
    }

    public function addToFile($name, $type, $x, $y, $z, $world, $yaw, $pitch, array $data = [], $skinData = "", $skinName = "")
    {
        if ($this->entityFile instanceof Config) {
            if ($type === 63) {
                $this->entityFile->set(++$this->entityCount, "type: " . $type . "\nname: " . $name . "\nx: " . $x . "\ny: " . $y . "\nz: " . $z . "\nworld: " . $world . "\nyaw: " . $yaw . "\npitch: " . $pitch . "\nskinName: " . $skinName . "\nskinData: " . $skinData);
            } else {
                $this->entityFile->set(++$this->entityCount, "type: " . $type . "\nname: " . $name . "\nx: " . $x . "\ny: " . $y . "\nz: " . $z . "\nworld: " . $world . "\nyaw: " . $yaw . "\npitch: " . $pitch);
            }
            $this->entityFile->save();
        } else {
            if ($type === 63) {
                file_put_contents($this->entityFilePath, ++$this->entityCount . ":\n  type: " . $type . "\n  name: " . $name . "\n  x: " . $x . "\n  y: " . $y . "\n  z: " . $z . "\n  world: " . $world . "\n  yaw: " . $yaw . "\n  pitch: " . $pitch . "\n  skinName: " . $skinName . "\n  skinData: " . $skinData);
            } else {
                $this->entityFile->set(++$this->entityCount, "type: " . $type . "\nname: " . $name . "\nx: " . $x . "\ny: " . $y . "\nz: " . $z . "\nworld: " . $world . "\nyaw: " . $yaw . "\npitch: " . $pitch);
            }
            $this->entityFile = new Config($this->entityFilePath);

        }
    }

    public function onPlayerRespawn(PlayerRespawnEvent $ev)
    {
        $num = 1;
        while ($num < $this->entityCount) {
            $entity = $this->entities[$num];
            if (isset($entity["type"])) {
                if ($entity["type"] === 63) {
                    $this->spawnHumanToPlayer($ev->getPlayer(), $entity["x"], $entity["y"], $entity["z"], $entity["yaw"], $entity["pitch"], $entity["name"], $entity["skinName"], $entity["skinData"]);
                }
            }
            $this->spawnEntityToPlayer($ev->getPlayer(), $entity["type"], $entity["x"], $entity["y"], $entity["z"], $entity["yaw"], $entity["pitch"]);
            $this->getLogger()->alert($this->prefix . "Invalid entity data for entity #" . $num . ". Please fix.");
            ++$num;
        }
    }

    public function spawnHumanToPlayer(Player $player, $x, $y, $z, $yaw, $pitch, $name, $skinName, $skinData)
    {
        $id = 100000 + $this->entityCount;
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
        $pk->item = 0;
        $pk->meta = 0;
        $player->dataPacket($pk);

        $add = new PlayerListPacket();
        $add->type = 0;
        $add->entries[] = [$uuid, $id, "", $skinName, $skinData];
        $player->dataPacket($add);
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
}