<?php

namespace ByNamles\PlayerShop\command;

use ByNamles\PlayerShop\forms\PSMainForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSCommand extends Command
{
    public function __construct()
    {
        parent::__construct("shop","Shop Form");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $sender->sendForm(new PSMainForm());
        }else{
            $sender->sendMessage(TextFormat::RED . "Use this command just in-game.");
        }
    }

}