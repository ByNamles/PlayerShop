<?php


namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSMainForm extends MenuForm{

    /** @var array */
    private static array $selectOptionIndex = [
        "register",
        "storage.list",
        "item.add",
        "item.remove",
        "sign.remove"
    ];

    /** @var PSMain */
    private PSMain $plugin;

    public function __construct(){
        $this->plugin = PSMain::getInstance();

        parent::__construct(TextFormat::GOLD . "Shop Menu", "", [
            new MenuOption(TextFormat::YELLOW . "Register"),
            new MenuOption(TextFormat::YELLOW . "Stock List"),
            new MenuOption(TextFormat::GREEN . "Item Add"),
            new MenuOption(TextFormat::RED . "Item Remove"),
            new MenuOption(TextFormat::RED . "Sign Remove")
        ], function (Player $player, int $selected): void {
            $playerName = $player->getName();
            switch(self::$selectOptionIndex[$selected]){
                case "register":
                    if(!$this->plugin->isRegisterPShop($playerName)){
                        $player->sendForm(new PSRegisterForm());
                    }else{
                        $player->sendMessage(TextFormat::RED . "You are already registered in the shop system.");
                    }
                    break;

                case "storage.list":
                    if($this->plugin->isRegisterPShop($playerName)){
                        if(count($this->plugin->getPSData($playerName)->get("Storage")) > 0){
                            $player->sendForm(new PSStorageForm($player));
                        }else{
                            $player->sendMessage(TextFormat::RED . "No item in stock.");
                        }
                    }else{
                        $player->sendMessage(TextFormat::RED . "Please register to the shop system first.");
                    }
                    break;

                case "item.add":
                    if($this->plugin->isRegisterPShop($playerName)){
                        $item = $player->getInventory()->getItemInHand();
                        if($item->getId() !== 0){
                            if($item->getName() == ItemFactory::getInstance()->get($item->getId(), $item->getMeta(), $item->getCount())->getName()){
                                $player->sendForm(new PSAddItemForm($item));
                            }else{
                                $player->sendMessage(TextFormat::RED . "You can't sell the renamed item.");
                            }
                        }else{
                            $player->sendMessage(TextFormat::RED . "Please take the item.");
                        }
                    }else{
                        $player->sendMessage(TextFormat::RED . "Please register to the shop system first.");
                    }
                    break;

                case  "item.remove":
                    if($this->plugin->isRegisterPShop($playerName)){
                        if(count($this->plugin->getPSData($playerName)->get("Storage")) > 0){
                            $player->sendForm(new PSRemoveItemForm($playerName));
                        }else{
                            $player->sendMessage(TextFormat::RED . "You dont have any items in stock that can be deleted");
                        }
                    }else{
                        $player->sendMessage(TextFormat::RED . "Please register to the shop system first.");
                    }
                    break;

                case "sign.remove":
                    if($this->plugin->isPlaceSign($playerName)){
                        $player->sendForm(new PSRemoveSignForm());
                    }else{
                        $player->sendMessage(TextFormat::RED . "You don't have any shop sign.");
                    }
                    break;
            }
        });
    }
}