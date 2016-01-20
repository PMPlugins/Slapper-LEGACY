<?php

namespace slapper;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Item\Item;
use pocketmine\nbt\tag\Byte;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Double;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\Float;
use pocketmine\nbt\tag\Int;
use pocketmine\nbt\tag\Short;
use pocketmine\nbt\tag\String;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use slapper\entities\other\SlapperBoat;
use slapper\entities\other\SlapperFallingSand;
use slapper\entities\other\SlapperMinecart;
use slapper\entities\other\SlapperPrimedTNT;
use slapper\entities\SlapperBat;
use slapper\entities\SlapperBlaze;
use slapper\entities\SlapperCaveSpider;
use slapper\entities\SlapperChicken;
use slapper\entities\SlapperCow;
use slapper\entities\SlapperCreeper;
use slapper\entities\SlapperEnderman;
use slapper\entities\SlapperGhast;
use slapper\entities\SlapperHuman;
use slapper\entities\SlapperIronGolem;
use slapper\entities\SlapperLavaSlime;
use slapper\entities\SlapperMushroomCow;
use slapper\entities\SlapperOcelot;
use slapper\entities\SlapperPig;
use slapper\entities\SlapperPigZombie;
use slapper\entities\SlapperSheep;
use slapper\entities\SlapperSilverfish;
use slapper\entities\SlapperSkeleton;
use slapper\entities\SlapperSlime;
use slapper\entities\SlapperSnowman;
use slapper\entities\SlapperSpider;
use slapper\entities\SlapperSquid;
use slapper\entities\SlapperVillager;
use slapper\entities\SlapperWolf;
use slapper\entities\SlapperZombie;
use slapper\entities\SlapperZombieVillager;


class main extends PluginBase implements Listener
{

    public $hitSessions;
    public $idSessions;
    public $updateSessions;
    public $prefix = (TextFormat::GREEN . "[" . TextFormat::YELLOW . "Slapper" . TextFormat::GREEN . "] ");
    public $noperm = (TextFormat::GREEN . "[" . TextFormat::YELLOW . "Slapper" . TextFormat::GREEN . "] You don't have permission.");
    public $helpHeader =
        (
            TextFormat::YELLOW . "---------- " .
            TextFormat::GREEN . "[" . TextFormat::YELLOW . "Slapper Help" . TextFormat::GREEN . "] " .
            TextFormat::YELLOW . "----------"
        );
    public $mainArgs = [
        "help: /slapper help",
        "spawn: /slapper spawn <type> [name]",
        "edit: /slapper edit [id] [args...]",
        "id: /slapper id",
        "remove: /slapper remove [id]",
        "version: /slapper version",
        "cancel: /slapper cancel",
        "updateall: /slapper updateall"
    ];
    public $editArgs = [
        "helmet: /slapper edit <eid> helmet <id>",
        "chestplate: /slapper edit <eid> <id>",
        "leggings: /slapper edit <eid> leggings <id>",
        "boots: /slapper edit <eid> boots <id>",
        "skin: /slapper edit <eid> skin",
        "name: /slapper edit <eid> name <name>",
        "addcommand: /slapper edit <eid> addcommand <command>",
        "delcommand: /slapper edit <eid> delcommand <command>",
        "listcommands: /slapper edit <eid> listcommands",
        "update: /slapper edit <eid> update",
        "block: /slapper edit <eid> block <id>",
        "tphere: /slapper edit <eid> tphere",
        "tpto: /slapper edit <eid> tpto",
        "menuname: /slapper edit <eid> menuname <name/remove>"
    ];

    public function onEnable()
    {
        $server = $this->getServer();
        if (
            stripos($server->getVersion(), "10") ||
            stripos($server->getVersion(), "11") ||
            stripos($server->getVersion(), "12")
        ) {
            $this->getLogger()->error($this->prefix . "Slapper currently only supports 0.13 servers or later. Disabling.");
            $server->getPluginManager()->disablePlugin($this);
        }
        $this->hitSessions = [];
        $this->idSessions = [];
        $this->updateSessions = [];
        Entity::registerEntity(SlapperCreeper::class, true);
        Entity::registerEntity(SlapperBat::class, true);
        Entity::registerEntity(SlapperSheep::class, true);
        Entity::registerEntity(SlapperPigZombie::class, true);
        Entity::registerEntity(SlapperGhast::class, true);
        Entity::registerEntity(SlapperBlaze::class, true);
        Entity::registerEntity(SlapperIronGolem::class, true);
        Entity::registerEntity(SlapperSnowman::class, true);
        Entity::registerEntity(SlapperOcelot::class, true);
        Entity::registerEntity(SlapperZombieVillager::class, true);
        Entity::registerEntity(SlapperHuman::class, true);
        Entity::registerEntity(SlapperVillager::class, true);
        Entity::registerEntity(SlapperZombie::class, true);
        Entity::registerEntity(SlapperSquid::class, true);
        Entity::registerEntity(SlapperCow::class, true);
        Entity::registerEntity(SlapperSpider::class, true);
        Entity::registerEntity(SlapperPig::class, true);
        Entity::registerEntity(SlapperMushroomCow::class, true);
        Entity::registerEntity(SlapperWolf::class, true);
        Entity::registerEntity(SlapperLavaSlime::class, true);
        Entity::registerEntity(SlapperSilverfish::class, true);
        Entity::registerEntity(SlapperSkeleton::class, true);
        Entity::registerEntity(SlapperSlime::class, true);
        Entity::registerEntity(SlapperChicken::class, true);
        Entity::registerEntity(SlapperEnderman::class, true);
        Entity::registerEntity(SlapperCaveSpider::class, true);
        Entity::registerEntity(SlapperBoat::class, true);
        Entity::registerEntity(SlapperMinecart::class, true);
        Entity::registerEntity(SlapperPrimedTNT::class, true);
        Entity::registerEntity(SlapperFallingSand::class, true);
        $this->getLogger()->debug("Entities have been registered!");
        $server->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->debug("Events have been registered!");
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        switch (strtolower($command->getName())) {
            case 'nothing':
                return true;
                break;
            case 'rca':
                if (count($args) < 2) {
                    $sender->sendMessage("Please enter a player and a command.");
                }
                $player = $this->getServer()->getPlayer(array_shift($args));
                if (!($player === null)) {
                    $this->getServer()->dispatchCommand($player, trim(implode(" ", $args)));
                }
                $sender->sendMessage(TextFormat::RED . "Player not found.");
                return true;
                break;
            case "slapper":
                if ($sender instanceof Player) {
                    if (!(isset($args[0]))) {
                        if ($sender->hasPermission("slapper.command") || $sender->hasPermission("slapper")) {
                            $sender->sendMessage($this->prefix . "Please type '/slapper help'.");
                            return true;
                        } else {
                            $sender->sendMessage($this->noperm);
                            return true;
                        }
                    }
                    $arg = array_shift($args);
                    switch ($arg) {
                        case "id":
                            if ($sender->hasPermission("slapper.id") || $sender->hasPermission("slapper")) {
                                $this->idSessions[$sender->getName()] = true;
                                $sender->sendMessage($this->prefix . "Hit an entity to get its ID!");
                                return true;
                            } else {
                                $sender->sendMessage($this->noperm);
                            }
                            return true;
                            break;
                        case "fixall":
                        case "updateall":
                        case "migrateall":
                            $server = $this->getServer();
                            $count = 0;
                            foreach ($server->getLevels() as $level) {
                                foreach ($level->getEntities() as $entity) {
                                    if (
                                        $entity instanceof SlapperHuman ||
                                        $entity instanceof SlapperSheep ||
                                        $entity instanceof SlapperPigZombie ||
                                        $entity instanceof SlapperVillager ||
                                        $entity instanceof SlapperCaveSpider ||
                                        $entity instanceof SlapperZombie ||
                                        $entity instanceof SlapperChicken ||
                                        $entity instanceof SlapperSpider ||
                                        $entity instanceof SlapperSilverfish ||
                                        $entity instanceof SlapperPig ||
                                        $entity instanceof SlapperCow ||
                                        $entity instanceof SlapperSlime ||
                                        $entity instanceof SlapperLavaSlime ||
                                        $entity instanceof SlapperEnderman ||
                                        $entity instanceof SlapperMushroomCow ||
                                        $entity instanceof SlapperBat ||
                                        $entity instanceof SlapperCreeper ||
                                        $entity instanceof SlapperSkeleton ||
                                        $entity instanceof SlapperSquid ||
                                        $entity instanceof SlapperWolf ||
                                        $entity instanceof SlapperGhast ||
                                        $entity instanceof SlapperZombieVillager ||
                                        $entity instanceof SlapperBlaze ||
                                        $entity instanceof SlapperOcelot ||
                                        $entity instanceof SlapperIronGolem ||
                                        $entity instanceof SlapperSnowman ||
                                        $entity instanceof SlapperMinecart ||
                                        $entity instanceof SlapperBoat ||
                                        $entity instanceof SlapperFallingSand ||
                                        $entity instanceof SlapperPrimedTNT
                                    ) {
                                        $count++;
                                        if (!($entity->namedtag->Commands)) {
                                            $entity->namedtag->Commands = new Compound("Commands", []);
                                        }
                                        $oldCmds = $this->getConfig()->get($entity->getName());
                                        if ($oldCmds) {
                                            foreach ($oldCmds as $oldCmd) {
                                                $entity->namedtag->Commands[$oldCmd] = new String($oldCmd, $oldCmd);
                                            }
                                        }
                                    }
                                    if ($entity instanceof SlapperHuman) {
                                        if ($entity->getSkinName() === "") {
                                            $entity->setSkin($entity->getSkinData(), "Standard_Custom");
                                            $entity->despawnFromAll();
                                            $entity->spawnToAll();
                                        }
                                    }
                                }
                            }
                            $sender->sendMessage($this->prefix . "Updated " . $count . " Slapper entities.");
                            return true;
                            break;
                        case "version":
                            if ($sender->hasPermission("slapper.version") || $sender->hasPermission("slapper")) {
                                $desc = $this->getDescription();
                                $sender->sendMessage($this->prefix . TextFormat::BLUE . $desc->getName() . " " . $desc->getVersion() . " " . TextFormat::GREEN . "by " . TextFormat::GOLD . "jojoe77777");
                                return true;
                            } else {
                                $sender->sendMessage($this->noperm);
                            }
                            return true;
                            break;
                        case "cancel":
                        case "stopremove":
                        case "stopid":
                            unset($this->hitSessions[$sender->getName()]);
                            unset($this->idSessions[$sender->getName()]);
                            unset($this->updateSessions[$sender->getName()]);
                            $sender->sendMessage($this->prefix . "Cancelled.");
                            return true;
                            break;
                        case "remove":
                            if ($sender->hasPermission("slapper.remove") || $sender->hasPermission("slapper")) {
                                if (isset($args[0])) {
                                    $entity = $sender->getLevel()->getEntity($args[0]);
                                    if (!($entity == null)) {
                                        if (
                                            $entity instanceof SlapperHuman ||
                                            $entity instanceof SlapperSheep ||
                                            $entity instanceof SlapperPigZombie ||
                                            $entity instanceof SlapperVillager ||
                                            $entity instanceof SlapperCaveSpider ||
                                            $entity instanceof SlapperZombie ||
                                            $entity instanceof SlapperChicken ||
                                            $entity instanceof SlapperSpider ||
                                            $entity instanceof SlapperSilverfish ||
                                            $entity instanceof SlapperPig ||
                                            $entity instanceof SlapperCow ||
                                            $entity instanceof SlapperSlime ||
                                            $entity instanceof SlapperLavaSlime ||
                                            $entity instanceof SlapperEnderman ||
                                            $entity instanceof SlapperMushroomCow ||
                                            $entity instanceof SlapperBat ||
                                            $entity instanceof SlapperCreeper ||
                                            $entity instanceof SlapperSkeleton ||
                                            $entity instanceof SlapperSquid ||
                                            $entity instanceof SlapperWolf ||
                                            $entity instanceof SlapperGhast ||
                                            $entity instanceof SlapperZombieVillager ||
                                            $entity instanceof SlapperBlaze ||
                                            $entity instanceof SlapperOcelot ||
                                            $entity instanceof SlapperIronGolem ||
                                            $entity instanceof SlapperSnowman ||
                                            $entity instanceof SlapperBoat ||
                                            $entity instanceof SlapperPrimedTNT ||
                                            $entity instanceof SlapperFallingSand ||
                                            $entity instanceof SlapperMinecart
                                        ) {
                                            if ($entity instanceof SlapperHuman) $entity->getInventory()->clearAll();
                                            $entity->kill();
                                            $sender->sendMessage($this->prefix . "Entity removed.");
                                        } else {
                                            $sender->sendMessage($this->prefix . "That entity is not handled by Slapper.");
                                        }
                                    } else {
                                        $sender->sendMessage($this->prefix . "Entity does not exist.");
                                    }
                                    return true;
                                }
                                $this->hitSessions[$sender->getName()] = true;
                                $sender->sendMessage($this->prefix . "Hit an entity to remove it.");
                            } else {
                                $sender->sendMessage($this->noperm);
                                return true;
                            }
                            return true;
                            break;
                        case "edit":
                            if ($sender->hasPermission("slapper.edit") || $sender->hasPermission("slapper")) {
                                if (isset($args[0])) {
                                    $entity = $sender->getLevel()->getEntity($args[0]);
                                    if (!($entity == null)) {
                                        if (
                                            $entity instanceof SlapperHuman ||
                                            $entity instanceof SlapperSheep ||
                                            $entity instanceof SlapperPigZombie ||
                                            $entity instanceof SlapperVillager ||
                                            $entity instanceof SlapperCaveSpider ||
                                            $entity instanceof SlapperZombie ||
                                            $entity instanceof SlapperChicken ||
                                            $entity instanceof SlapperSpider ||
                                            $entity instanceof SlapperSilverfish ||
                                            $entity instanceof SlapperPig ||
                                            $entity instanceof SlapperCow ||
                                            $entity instanceof SlapperSlime ||
                                            $entity instanceof SlapperLavaSlime ||
                                            $entity instanceof SlapperEnderman ||
                                            $entity instanceof SlapperMushroomCow ||
                                            $entity instanceof SlapperBat ||
                                            $entity instanceof SlapperCreeper ||
                                            $entity instanceof SlapperSkeleton ||
                                            $entity instanceof SlapperSquid ||
                                            $entity instanceof SlapperWolf ||
                                            $entity instanceof SlapperGhast ||
                                            $entity instanceof SlapperZombieVillager ||
                                            $entity instanceof SlapperBlaze ||
                                            $entity instanceof SlapperOcelot ||
                                            $entity instanceof SlapperIronGolem ||
                                            $entity instanceof SlapperSnowman ||
                                            $entity instanceof SlapperMinecart ||
                                            $entity instanceof SlapperBoat ||
                                            $entity instanceof SlapperFallingSand ||
                                            $entity instanceof SlapperPrimedTNT
                                        ) {
                                            if (isset($args[1])) {
                                                switch ($args[1]) {
                                                    case "helm":
                                                    case "helmet":
                                                    case "head":
                                                    case "hat":
                                                    case "cap":
                                                        if ($entity instanceof SlapperHuman) {
                                                            if (isset($args[2])) {
                                                                $entity->getInventory()->setHelmet(Item::fromString($args[2]));
                                                                $sender->sendMessage($this->prefix . "Helmet updated.");
                                                            } else {
                                                                $sender->sendMessage($this->prefix . "Please enter an item ID.");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can not wear armor.");
                                                        }
                                                        return true;
                                                    case "chest":
                                                    case "shirt":
                                                    case "chestplate":
                                                        if ($entity instanceof SlapperHuman) {
                                                            if (isset($args[2])) {
                                                                $entity->getInventory()->setChestplate(Item::fromString($args[2]));
                                                                $sender->sendMessage($this->prefix . "Chestplate updated.");
                                                            } else {
                                                                $sender->sendMessage($this->prefix . "Please enter an item ID.");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can not wear armor.");
                                                        }
                                                        return true;
                                                    case "pants":
                                                    case "legs":
                                                    case "leggings":
                                                        if ($entity instanceof SlapperHuman) {
                                                            if (isset($args[2])) {
                                                                $entity->getInventory()->setLeggings(Item::fromString($args[2]));
                                                                $sender->sendMessage($this->prefix . "Leggings updated.");
                                                            } else {
                                                                $sender->sendMessage($this->prefix . "Please enter an item ID.");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can not wear armor.");
                                                        }
                                                        return true;
                                                    case "feet":
                                                    case "boots":
                                                    case "shoes":
                                                        if ($entity instanceof SlapperHuman) {
                                                            if (isset($args[2])) {
                                                                $entity->getInventory()->setBoots(Item::fromString($args[2]));
                                                                $sender->sendMessage($this->prefix . "Boots updated.");
                                                            } else {
                                                                $sender->sendMessage($this->prefix . "Please enter an item ID.");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can not wear armor.");
                                                        }
                                                        return true;
                                                    case "hand":
                                                    case "item":
                                                    case "holding":
                                                    case "arm":
                                                    case "held":
                                                        if ($entity instanceof SlapperHuman) {
                                                            if (isset($args[2])) {
                                                                $entity->getInventory()->setItemInHand(Item::fromString($args[2]));
                                                                $sender->sendMessage($this->prefix . "Item updated.");
                                                            } else {
                                                                $sender->sendMessage($this->prefix . "Please enter an item ID.");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can not wear armor.");
                                                        }
                                                        return true;
                                                    case "setskin":
                                                    case "changeskin":
                                                    case "editskin";
                                                    case "skin":
                                                        if ($entity instanceof SlapperHuman) {
                                                            $entity->setSkin($sender->getSkinData(), $sender->getSkinName());
                                                            $entity->despawnFromAll();
                                                            $entity->spawnToAll();
                                                            $sender->sendMessage($this->prefix . "Skin updated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can't have a skin.");
                                                        }
                                                        return true;
                                                    case "name":
                                                    case "customname":
                                                        if (isset($args[2])) {
                                                            array_shift($args);
                                                            array_shift($args);
                                                            $entity->setDataProperty(2, Entity::DATA_TYPE_STRING, trim(implode(" ", $args)));
                                                            $entity->despawnFromAll();
                                                            $entity->spawnToAll();
                                                            $sender->sendMessage($this->prefix . "Name updated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "Please enter a name.");
                                                        }
                                                        return true;
                                                    case "listname":
                                                    case "nameonlist":
                                                    case "menuname":
                                                        if ($entity instanceof SlapperHuman) {
                                                            if (isset($args[2])) {
                                                                $type = 0;
                                                                array_shift($args);
                                                                array_shift($args);
                                                                $input = trim(implode(" ", $args));
                                                                switch (strtolower($input)) {
                                                                    case "remove":
                                                                    case "":
                                                                    case "disable":
                                                                    case "off":
                                                                    case "hide":
                                                                        $type = 1;
                                                                }
                                                                if ($type === 0) {
                                                                    $entity->namedtag->MenuName = new String("MenuName", $input);
                                                                } else {
                                                                    $entity->namedtag->MenuName = new String("MenuName", "");
                                                                }
                                                                $entity->despawnFromAll();
                                                                $entity->spawnToAll();
                                                                $sender->sendMessage($this->prefix . "Menu name updated.");
                                                            } else {
                                                                $sender->sendMessage($this->prefix . "Please enter a menu name.");
                                                                return true;
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity can not have a menu name.");
                                                        }
                                                        return true;
                                                        break;
                                                    case "namevisible":
                                                    case "customnamevisible":
                                                    case "tagvisible":
                                                    case "name_visible":
                                                    case "custom_name_visible":
                                                    case "tag_visible":
                                                        if (isset($args[2])) {
                                                            $entity->setDataProperty(3, 0, intval($args[2]));
                                                            $entity->despawnFromAll();
                                                            $entity->spawnToAll();
                                                            $sender->sendMessage($this->prefix . "Name visibility updated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "Please enter a value, 1 or 0.");
                                                        }
                                                        return true;
                                                    case "addc":
                                                    case "addcmd":
                                                    case "addcommand":
                                                        if (isset($args[2])) {
                                                            array_shift($args);
                                                            array_shift($args);
                                                            $input = trim(implode(" ", $args));
                                                            if (isset($entity->namedtag->Commands[$input])) {
                                                                $sender->sendMessage($this->prefix . "That command has already been added.");
                                                                return true;
                                                            }
                                                            $entity->namedtag->Commands[$input] = new String($input, $input);
                                                            $sender->sendMessage($this->prefix . "Command added.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "Please enter a command.");
                                                        }
                                                        return true;
                                                    case "delc":
                                                    case "delcmd":
                                                    case "removecommand":
                                                        if (isset($args[2])) {
                                                            array_shift($args);
                                                            array_shift($args);
                                                            $input = trim(implode(" ", $args));
                                                            unset($entity->namedtag->Commands[$input]);
                                                            $sender->sendMessage($this->prefix . "Command removed.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "Please enter a command.");
                                                        }
                                                        return true;
                                                    case "listcommands":
                                                    case "listcmds":
                                                    case "listcs":
                                                        if (!(empty($entity->namedtag->Commands))) {
                                                            $id = 0;
                                                            foreach ($entity->namedtag->Commands as $cmd) {
                                                                $id++;
                                                                $sender->sendMessage(TextFormat::GREEN . "[" . TextFormat::YELLOW . "S" . TextFormat::GREEN . "] " . TextFormat::YELLOW . $id . ". " . TextFormat::GREEN . $cmd . "\n");
                                                            }
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity does not have any commands.");
                                                        }
                                                        return true;
                                                    case "update":
                                                    case "fix":
                                                    case "migrate":
                                                        if ($this->getConfig()->get($entity->getName()) !== false) {
                                                            foreach ($this->getConfig()->get($entity->getName()) as $cmd) {
                                                                $entity->namedtag->Commands[$cmd] = new String($cmd, $cmd);
                                                            }
                                                            $sender->sendMessage($this->prefix . "Commands migrated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "No old commands found.");
                                                        }
                                                        return true;
                                                    case "block":
                                                    case "tile":
                                                    case "blockid":
                                                    case "tileid":
                                                        if ($entity instanceof SlapperFallingSand) {
                                                            $entity->namedtag->BlockID = new Int("BlockID", intval($args[2]));
                                                            $entity->spawnToAll();
                                                            $sender->sendMessage($this->prefix . "Block updated.");
                                                        } else {
                                                            $sender->sendMessage($this->prefix . "That entity is not a block.");
                                                        }
                                                        return true;
                                                        break;
                                                    case "teleporthere":
                                                    case "tphere":
                                                    case "movehere":
                                                    case "bringhere":
                                                        $entity->teleport($sender);
                                                        $sender->sendMessage($this->prefix . "Teleported entity to you.");
                                                        $entity->despawnFromAll();
                                                        $entity->spawnToAll();
                                                        return true;
                                                        break;
                                                    case "teleportto":
                                                    case "tpto":
                                                    case "goto":
                                                    case "teleport":
                                                    case "tp":
                                                        $sender->teleport($entity);
                                                        $sender->sendMessage($this->prefix . "Teleported you to entity.");
                                                        return true;
                                                        break;
                                                    default:
                                                        $sender->sendMessage($this->prefix . "Unknown command.");
                                                        return true;
                                                }
                                            } else {
                                                $sender->sendMessage($this->helpHeader);
                                                foreach ($this->editArgs as $msgArg) {
                                                    $sender->sendMessage(str_ireplace("<eid>", $args[0], (TextFormat::GREEN . " - " . $msgArg . "\n")));
                                                }
                                                return true;
                                            }
                                        } else {
                                            $sender->sendMessage($this->prefix . "That entity is not handled by Slapper.");
                                        }
                                    } else {
                                        $sender->sendMessage($this->prefix . "Entity does not exist.");
                                    }
                                    return true;
                                } else {
                                    $sender->sendMessage($this->helpHeader);
                                    foreach ($this->editArgs as $msgArg) {
                                        $sender->sendMessage(TextFormat::GREEN . " - " . $msgArg . "\n");
                                    }
                                    return true;
                                }
                                $this->hitSessions[$sender->getName()] = true;
                                $sender->sendMessage($this->prefix . "Hit an entity to remove it.");
                            } else {
                                $sender->sendMessage($this->prefix . "You don't have permission.");
                            }
                            return true;
                            break;
                        case "help":
                        case "?":
                            $sender->sendMessage($this->helpHeader);
                            foreach ($this->mainArgs as $msgArg) {
                                $sender->sendMessage(TextFormat::GREEN . " - " . $msgArg . "\n");
                            }
                            return true;
                            break;
                        case "add":
                        case "make":
                        case "create":
                        case "spawn":
                            $type = array_shift($args);
                            $spawn = true;
                            $name = str_replace("{color}", "§", str_replace("{line}", "\n", trim(implode(" ", $args))));
                            if ($type === null || $type === "" || $type === " ") {
                                $sender->sendMessage($this->prefix . "Please enter an entity type.");
                                return true;
                            }
                            $defaultName = $sender->getDisplayName();
                            if ($name == null) $name = $defaultName;
                            $playerX = $sender->getX();
                            $playerY = $sender->getY();
                            $playerZ = $sender->getZ();
                            $inventory = $sender->getInventory();
                            $theOne = "Blank";
                            foreach ([
                                         "Chicken",
                                         "ZombiePigman",
                                         "Pig",
                                         "Sheep",
                                         "Cow",
                                         "Mooshroom",
                                         "MushroomCow",
                                         "Wolf",
                                         "Enderman",
                                         "Spider",
                                         "Skeleton",
                                         "PigZombie",
                                         "Creeper",
                                         "Slime",
                                         "Silverfish",
                                         "Villager",
                                         "Zombie",
                                         "Human",
                                         "Player",
                                         "Squid",
                                         "Bat",
                                         "CaveSpider",
                                         "LavaSlime",
                                         "Ghast",
                                         "Ocelot",
                                         "Blaze",
                                         "ZombieVillager",
                                         "VillagerZombie",
                                         "Snowman",
                                         "SnowGolem",
                                         "Minecart",
                                         "FallingSand",
                                         "FallingBlock",
                                         "FakeBlock",
                                         "Boat",
                                         "PrimedTNT"
                                     ] as $entityType) {
                                if (strtolower($type) === strtolower($entityType)) {
                                    $theOne = $entityType;
                                }
                            }
                            $typeToUse = "Nothing";
                            switch ($theOne) {
                                case "Human":
                                    $typeToUse = "SlapperHuman";
                                    break;
                                case "Player":
                                    $typeToUse = "SlapperHuman";
                                    break;
                                case "Pig":
                                    $typeToUse = "SlapperPig";
                                    break;
                                case "Bat":
                                    $typeToUse = "SlapperBat";
                                    break;
                                case "Cow":
                                    $typeToUse = "SlapperCow";
                                    break;
                                case "Sheep":
                                    $typeToUse = "SlapperSheep";
                                    break;
                                case "MushroomCow":
                                    $typeToUse = "SlapperMushroomCow";
                                    break;
                                case "Mooshroom":
                                    $typeToUse = "SlapperMushroomCow";
                                    break;
                                case "LavaSlime":
                                    $typeToUse = "SlapperLavaSlime";
                                    break;
                                case "Enderman":
                                    $typeToUse = "SlapperEnderman";
                                    break;
                                case "Zombie":
                                    $typeToUse = "SlapperZombie";
                                    break;
                                case "Creeper":
                                    $typeToUse = "SlapperCreeper";
                                    break;
                                case "Skeleton":
                                    $typeToUse = "SlapperSkeleton";
                                    break;
                                case "Silverfish":
                                    $typeToUse = "SlapperSilverfish";
                                    break;
                                case "Chicken":
                                    $typeToUse = "SlapperChicken";
                                    break;
                                case "Villager":
                                    $typeToUse = "SlapperVillager";
                                    break;
                                case "CaveSpider":
                                    $typeToUse = "SlapperCaveSpider";
                                    break;
                                case "Spider":
                                    $typeToUse = "SlapperSpider";
                                    break;
                                case "Squid":
                                    $typeToUse = "SlapperSquid";
                                    break;
                                case "Wolf":
                                    $typeToUse = "SlapperWolf";
                                    break;
                                case "Slime":
                                    $typeToUse = "SlapperSlime";
                                    break;
                                case "PigZombie":
                                    $typeToUse = "SlapperPigZombie";
                                    break;
                                case "MagmaCube":
                                    $typeToUse = "SlapperLavaSlime";
                                    break;
                                case "ZombiePigman":
                                    $typeToUse = "SlapperPigZombie";
                                    break;

                                case "PrimedTNT":
                                    $typeToUse = "SlapperPrimedTNT";
                                    break;
                                case "Minecart":
                                    $typeToUse = "SlapperMinecart";
                                    break;
                                case "Boat":
                                    $typeToUse = "SlapperBoat";
                                    break;
                                case "FallingSand":
                                    $typeToUse = "SlapperFallingSand";
                                    break;
                                case "FallingBlock":
                                    $typeToUse = "SlapperFallingSand";
                                    break;
                                case "FakeBlock":
                                    $typeToUse = "SlapperFallingSand";
                                    break;
                            }
                            switch ($theOne) {
                                case "ZombieVillager":
                                    $typeToUse = "SlapperZombieVillager";
                                    break;
                                case "VillagerZombie":
                                    $typeToUse = "SlapperZombieVillager";
                                    break;
                                case "Ghast":
                                    $typeToUse = "SlapperGhast";
                                    break;
                                case "Blaze":
                                    $typeToUse = "SlapperBlaze";
                                    break;
                                case "IronGolem":
                                    $typeToUse = "SlapperIronGolem";
                                    break;
                                case "VillagerGolem":
                                    $typeToUse = "SlapperIronGolem";
                                    break;
                                case "SnowGolem":
                                    $typeToUse = "SlapperSnowman";
                                    break;
                                case "Snowman":
                                    $typeToUse = "SlapperSnowman";
                                    break;
                                case "Ocelot":
                                    $typeToUse = "SlapperOcelot";
                                    break;
                            }
                            if (!($typeToUse === "Nothing") && !($theOne === "Blank")) {
                                $nbt = $this->makeNBT($sender->getSkinData(), $sender->getSkinName(), $name, $inventory, $sender->getYaw(), $sender->getPitch(), $playerX, $playerY, $playerZ);
                                $slapperEntity = Entity::createEntity($typeToUse, $sender->getLevel()->getChunk($playerX >> 4, $playerZ >> 4), $nbt);
                                $sender->sendMessage($this->prefix . $theOne . " entity spawned with name " . TextFormat::WHITE . "\"" . TextFormat::BLUE . $name . TextFormat::WHITE . "\"" . TextFormat::GREEN . " and entity ID " . TextFormat::BLUE . $slapperEntity->getId());
                            }
                            if ($typeToUse === "SlapperHuman") {
                                $Inv = $slapperEntity->getInventory();

                                $Inv->setHelmet($inventory->getHelmet());
                                $Inv->setChestplate($inventory->getChestplate());
                                $Inv->setLeggings($inventory->getLeggings());
                                $Inv->setBoots($inventory->getBoots());
                                $slapperEntity->getInventory()->setHeldItemSlot($inventory->getHeldItemSlot());
                                $slapperEntity->getInventory()->setItemInHand($inventory->getItemInHand());
                            }
                            if (!($theOne == "Blank")) $slapperEntity->spawnToAll();
                            if ($typeToUse === "Nothing" || $theOne === "Blank") {
                                if ($spawn) $sender->sendMessage($this->prefix . "Invalid entity.");
                            }
                            return true;
                        default:
                            $sender->sendMessage($this->prefix . "Unknown command. Type '/slapper help' for help.");
                            return true;
                    }
                    return true;
                } else {
                    $sender->sendMessage($this->prefix . "This command only works in game.");
                    return true;
                }
        }
        return true;
    }

    private function makeNBT($skin, $skinName, $name, $inv, $yaw, $pitch, $x, $y, $z)
    {
        $nbt = new Compound;
        $nbt->Pos = new Enum("Pos", [
            new Double("", $x),
            new Double("", $y),
            new Double("", $z)
        ]);
        $nbt->Rotation = new Enum("Rotation", [
            new Float("", $yaw),
            new Float("", $pitch)
        ]);
        $nbt->Health = new Short("Health", 1);
        $nbt->Inventory = new Enum("Inventory", $inv);
        $nbt->CustomName = new String("CustomName", $name);
        $nbt->CustomNameVisible = new Byte("CustomNameVisible", 1);
        $nbt->Invulnerable = new Byte("Invulnerable", 1);
        $nbt->Skin = new Compound("Skin", [
            "Data" => new String("Data", $skin),
            "Name" => new String("Name", $skinName)
        ]);
        /* Slapper NBT info */
        $nbt->Commands = new Compound("Commands", []);
        $nbt->MenuName = new String("MenuName", "");
        $nbt->SlapperVersion = new String("SlapperVersion", "1.2.9");
        /* FallingSand Block ID */
        $nbt->BlockID = new Int("BlockID", 1);
        /* Name visible */
        $nbt->CustomNameVisible = new Byte("CustomNameVisible", 1);

        return $nbt;
    }

    /**
     * @ignoreCancelled true
     */
    public function onEntityDamage(EntityDamageEvent $event)
    {
        $perm = true;
        $taker = $event->getEntity();
        if (
            $taker instanceof SlapperHuman ||
            $taker instanceof SlapperSheep ||
            $taker instanceof SlapperPigZombie ||
            $taker instanceof SlapperVillager ||
            $taker instanceof SlapperCaveSpider ||
            $taker instanceof SlapperZombie ||
            $taker instanceof SlapperChicken ||
            $taker instanceof SlapperSpider ||
            $taker instanceof SlapperSilverfish ||
            $taker instanceof SlapperPig ||
            $taker instanceof SlapperCow ||
            $taker instanceof SlapperSlime ||
            $taker instanceof SlapperLavaSlime ||
            $taker instanceof SlapperEnderman ||
            $taker instanceof SlapperMushroomCow ||
            $taker instanceof SlapperBat ||
            $taker instanceof SlapperCreeper ||
            $taker instanceof SlapperSkeleton ||
            $taker instanceof SlapperSquid ||
            $taker instanceof SlapperWolf ||
            $taker instanceof SlapperGhast ||
            $taker instanceof SlapperZombieVillager ||
            $taker instanceof SlapperBlaze ||
            $taker instanceof SlapperOcelot ||
            $taker instanceof SlapperIronGolem ||
            $taker instanceof SlapperSnowman ||
            $taker instanceof SlapperBoat ||
            $taker instanceof SlapperPrimedTNT ||
            $taker instanceof SlapperFallingSand ||
            $taker instanceof SlapperMinecart
        ) {
            if (!($event instanceof EntityDamageByEntityEvent)) $event->setCancelled(true);
            if ($event instanceof EntityDamageByEntityEvent) {
                $hitter = $event->getDamager();
                if (!$hitter instanceof Player) {
                    $event->setCancelled(true);
                }
                if ($hitter instanceof Player) {
                    $giverName = $hitter->getName();
                    if ($hitter instanceof Player) {
                        if (isset($this->hitSessions[$giverName])) {
                            if ($taker instanceof SlapperHuman) $taker->getInventory()->clearAll();
                            $taker->kill();
                            unset($this->hitSessions[$giverName]);
                            $hitter->sendMessage($this->prefix . "Entity removed.");
                            return;
                        }
                        if (isset($this->idSessions[$giverName])) {
                            $hitter->sendMessage($this->prefix . "Entity ID: " . $taker->getId());
                            unset($this->idSessions[$giverName]);
                            $event->setCancelled();
                            return;
                        }
                        if (!($hitter->hasPermission("slapper.hit"))) {
                            $event->setCancelled(true);
                            $perm = false;
                        }
                        if ($perm == false) {
                            if (isset($taker->namedtag->Commands)) {
                                foreach ($taker->namedtag->Commands as $cmd) {
                                    $this->getServer()->dispatchCommand(new ConsoleCommandSender(), str_ireplace("{player}", $giverName, $cmd));
                                }
                            } else {
                                $this->getLogger()->debug("Outdated entity; adding blank commands compound. Please restore commands manually with '/slapper edit " . $taker->getId() . " fix'");
                                $taker->namedtag->Commands = new Compound("Commands", []);
                            }
                        }
                    }

                }
            }
        }

    }
}
