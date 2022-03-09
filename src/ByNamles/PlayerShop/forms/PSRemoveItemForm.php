<?php

namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\forms\textures\PSButtonImage;
use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSRemoveItemForm extends MenuForm{

    /** @var PSMain */
    private PSMain $plugin;
    /** @var array */
    protected array $price;

    public function __construct(string $player){
        $this->plugin = PSMain::getInstance();

        parent::__construct(TextFormat::GOLD . "Item Remove Menu",
            TextFormat::DARK_RED . "ID:Meta - Amount, Touch the item you want to get back and select the amount.",
            $this->getItemOptions($player),
        function (Player $player, int $selected): void {
            $selectedDecode = explode("-", TextFormat::clean($this->getOption($selected)->getText()));
            $idDecode = explode(":", $selectedDecode[0]);
            $player->sendForm(new PSRemoveItemCountForm(ItemFactory::getInstance()->get((int)$idDecode[0], (int)$idDecode[1], 1), $selectedDecode[1], $this->price[$idDecode[0] . ":" . $idDecode[1] . "-" . $selectedDecode[1]]));
        });
    }

    public function getItemOptions(string $player) : array{
        $options = [];
        foreach($this->plugin->getPSData($player)->get("Storage") as $key => $value){
            $itemDecode = explode(":", $key);
            $item = ItemFactory::getInstance()->get((int)$itemDecode[0], (int)$itemDecode[1], (int)$value);
            $hash = $item->getId() . ":" . $item->getMeta() . "-" . $item->getCount();
            $this->price[$hash] = $itemDecode[2];
            $options[] = new MenuOption(TextFormat::AQUA . $hash, PSButtonImage::getItemPNG($item->getId(), $item->getMeta()));
        }
        return $options;
    }
}