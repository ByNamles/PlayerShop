<?php


namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\forms\textures\PSButtonImage;
use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSOpenShopForm extends MenuForm{

    /** @var PSMain */
    protected PSMain $plugin;
    /** @var array */
    protected array $optionsIndex = [];
    /** @var string */
    protected string $owner;

    public function __construct(Player $player, string $owner){
        $this->plugin = PSMain::getInstance();
        $this->owner = $owner;

        parent::__construct(TextFormat::GREEN . $owner . TextFormat::GOLD . " Shop",
            TextFormat::GREEN . "Touch the item you want to buy and select the amount.",
            $this->getOptionsList($owner),
        function (Player $player, int $selected): void {
            $select = $this->optionsIndex[$selected];
            $decodeHash = explode("-", $select);
            $decodeItemData = explode(":", $decodeHash[0]);
            $item = ItemFactory::getInstance()->get((int)$decodeItemData[0], (int)$decodeItemData[1], (int)$decodeHash[1]);
            $player->sendForm(new PSBuyCountForm($item, $item->getCount(), $decodeItemData[2], $this->owner));
        });
    }

    public function getOptionsList(string $player) : array{
        $optionList = $this->plugin->getOptionsList($player);
        $this->optionsIndex = $optionList;
        $options = [];
        foreach($optionList as $key => $value){
            $decodeHash = explode("-", $value);
            $decodeItemData = explode(":", $decodeHash[0]);
            $item = ItemFactory::getInstance()->get((int)$decodeItemData[0], (int)$decodeItemData[1], (int)$decodeHash[1]);
            $options[] = new MenuOption(TextFormat::GREEN . "Item: " . TextFormat::WHITE .$item->getName() . TextFormat::GREEN . " Stock: " . TextFormat::WHITE . $item->getCount() . TextFormat::GREEN . " Price: " . TextFormat::WHITE .$decodeItemData[2], PSButtonImage::getItemPNG($item->getId(), $item->getMeta()));
        }
        return $options;
    }
}