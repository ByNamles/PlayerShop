<?php


namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\ModalForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSRemoveSignForm extends ModalForm{

    /** @var PSMain */
    protected PSMain $plugin;

    public function __construct(){
        $this->plugin = PSMain::getInstance();

        parent::__construct(TextFormat::RED . "Need Confirm", TextFormat::GREEN . "Are you confirm to delete sign ?",
        function (Player $player, bool $choice): void{
            if ($choice){
                $this->plugin->deleteShopSignData($player->getName());
                $player->sendMessage(TextFormat::GREEN . "Your shop sign has been deleted.");
            }
            else $player->sendMessage(TextFormat::RED . "Process cancelled.");
        }
        );
    }
}