<?php

namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\CustomFormResponse;
use onebone\economyapi\EconomyAPI;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Label;
use dktapps\pmforms\element\Slider;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSBuyCountForm extends CustomForm{
    
    /** @var PSMain */
    protected PSMain $plugin;
    /** @var Item */
    protected Item $item;
    /** @var int */
    protected int $price;
    /** @var int */
    protected int $maxCount;
    /** @var string */
    protected string $owner;

    public function __construct(Item $item, int $maxCount, int $price, string $owner){
        $this->plugin = PSMain::getInstance();
        $this->item = $item;
        $this->price = $price;
        $this->maxCount = $maxCount;
        $this->owner = $owner;
        
        if($maxCount < 1) $maxCount = 1;
        parent::__construct(TextFormat::GREEN . "Amount Determine Menu", [
            new Label("element0",TextFormat::AQUA . "Item to buy: " . TextFormat::DARK_GRAY . $item->getName()),
            new Label("element1",TextFormat::AQUA . "Price(per amount): " . TextFormat::DARK_GRAY . $price),
            $maxCount > 1 ? new Slider("element2",TextFormat::GOLD . "The amount you want to buy " . TextFormat::DARK_GRAY, 1, $maxCount) : new Label("element2",TextFormat::GOLD . "The amount you want to buy: " . TextFormat::WHITE . "1")
        ],function (Player $player, CustomFormResponse $response): void {
            $economy = EconomyAPI::getInstance();

            $hash = $this->item->getId() . ":" . $this->item->getMeta() . ":" . $this->price;

            $count = $this->maxCount > 1 ? $response->getFloat("element2") : 1;
            $price = $count * $this->price;
            $this->item->setCount($count);

            if($economy->myMoney($player) >= $price){
                if($player->getInventory()->canAddItem($this->item)){
                    if($this->plugin->getControlCount($this->owner, $hash, $count)){
                        $player->getInventory()->addItem($this->item);
                        $economy->reduceMoney($player, $price);
                        $economy->addMoney($this->plugin->getServer()->getPlayerByPrefix($this->owner), $price);
                        $this->plugin->removeItemToPlayer($this->item, $this->owner, $count, $this->price);
                        $player->sendMessage(TextFormat::GREEN."You bought " . $this->item->getCount() . " " . $this->item->getName());
                    }else{
                        $player->sendMessage(TextFormat::RED . "Someone else bought it before you.");
                    }
                }else{
                    $player->sendMessage(TextFormat::RED . "Your inventory is full.");
                }
            }else{
                $player->sendMessage(TextFormat::RED . "You don't have enough money to buy this item.");
            }
        });
    }
}