<?php

namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\CustomFormElement;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use dktapps\pmforms\element\Input;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSAddItemForm extends CustomForm{

    /** @var PSMain */
    private PSMain $plugin;
    /** @var Item */
    protected Item $item;

    public function __construct(Item $item){
        $this->plugin = PSMain::getInstance();
        $this->item = $item;

        parent::__construct(TextFormat::GOLD . "Shop Add Item Menu", [
            new Label("element0",TextFormat::LIGHT_PURPLE . "Item to add: " . TextFormat::DARK_GRAY . $item->getName()),
            $this->getItemCount(),
            new Input("element2",TextFormat::LIGHT_PURPLE . "Price per amount: ", TextFormat::DARK_GRAY . "Write a price.")
        ], function (Player $player, CustomFormResponse $response): void{
            $count = $this->item->getCount() > 1 ? $response->getString("element1") : 1;
            $price = $response->getString("element2");
            if(!($price) >= 0){
                    $item = ItemFactory::getInstance()->get($this->item->getId(), $this->item->getMeta(), $count);
                    $this->plugin->addItemToPlayer($item, $player->getName(), $price, $count);
                    $player->getInventory()->removeItem($item);
                    $player->sendMessage(TextFormat::GREEN . "The item has been added to your shop.");
            }else{
                $player->sendMessage(TextFormat::RED . "The item price should be numeric and not empty.");
            }

        });
    }

    public function getItemCount() : CustomFormElement{
        return $this->item->getCount() > 1 ? new Slider("element1",TextFormat::LIGHT_PURPLE . "Amount to be added to shop: " . TextFormat::DARK_GRAY, 1, $this->item->getCount()) : new Label("element1",TextFormat::LIGHT_PURPLE . "Amount to be added to shop: " . TextFormat::DARK_GRAY . "1");
    }

}