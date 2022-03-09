<?php


namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSRemoveItemCountForm extends CustomForm{

    /** @var PSMain */
    private PSMain$plugin;
    /** @var int */
    protected int $count;
    /** @var Item */
    protected Item $item;
    /** @var int */
    protected int $price;

    public function __construct(Item $item, int $count, int $price){
        $this->plugin = PSMain::getInstance();
        $this->count = $count;
        $this->item = $item;
        $this->price = $price;

        parent::__construct(TextFormat::GOLD . "Item Remove Amount Menu", [
            new Label("element0",TextFormat::GREEN . "Item to remove: " . TextFormat::LIGHT_PURPLE . $item->getName()),
            $count > 1 ? new Slider("element1",TextFormat::GREEN . "Select amount " . TextFormat::LIGHT_PURPLE, 1, $count) : new Label("element1",TextFormat::GREEN . "Amount: " . TextFormat::WHITE . "1")
        ],function (Player $player, CustomFormResponse $response): void {
            $count = $this->count > 1 ? $response->getString("element1") : 1;
            $this->item->setCount($count);
            $player->getInventory()->addItem($this->item);
            $this->plugin->removeItemToPlayer($this->item, $player->getName(), $count, $this->price);
            $player->sendMessage(TextFormat::GREEN . "Your item has been added to your inventory.");
        });
    }
}