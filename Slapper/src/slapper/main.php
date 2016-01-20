<?php

namespace slapper;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;


class main extends PluginBase implements Listener
{
    public $entityNames = [
        "Human" => ["Human", "Player", "FakePlayer", "Statue", "Person", "Guy", "Dude"],
        "IronGolem" => ["IronGolem", "VillagerGolem", "IronMan"],
        "Sheep" => ["Sheep", "Lamb"]
    ];
    public $prefix = TextFormat::GREEN . "[" . TextFormat::YELLOW . "Slapper" . TextFormat::GREEN . "] ";
    public $entityCount = 0;
    public $entityFile = null;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("entities.yml");
        $path = $this->getDataFolder() . "entities.yml";
        $file = file_get_contents($path);
        if(empty($file)){
            $this->entityFile = null;
        } else {
            $this->entityFile = new Config($path);
            $this->entityCount = count($this->entityFile->getAll());
        }
    }

    public function createEntity($type)
    {

    }


    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $isConsole = !$sender instanceof Player;
        switch ($command->getName()) {
            case "slapper":
                if (isset($args[0])) {
                    switch ($args[0]) {
                        case "create":
                        case "spawn":
                            if ($isConsole) {
                                $sender->sendMessage($this->prefix . "Please run this command ingame.");
                                return true;
                            }
                            if (isset($args[1])) {
                                foreach ($this->entityNames as $nicknames) {
                                    foreach ($nicknames as $nickname) {
                                        if (strtolower($args[1]) === strtolower($nickname)) {
                                            $type = $nicknames[0];
                                        }
                                    }
                                }
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
    }
}
