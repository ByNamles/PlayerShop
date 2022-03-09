<?php


namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Label;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSStorageForm extends CustomForm{

    /** @var PSMain */
    private PSMain $plugin;

    public function __construct(Player $player){
        $this->plugin = PSMain::getInstance();

        parent::__construct(TextFormat::GOLD . "Stock List", [
            new Label("element0",$this->plugin->listStorage($player->getName())),
        ], function (): void{});
    }
}