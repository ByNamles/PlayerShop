<?php


namespace ByNamles\PlayerShop;

use ByNamles\PlayerShop\forms\PSOpenShopForm;
use JsonException;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class PSEventListener implements Listener{

    /**
     * @throws JsonException
     */
    public function onSign(SignChangeEvent $event){
        $player = $event->getPlayer();
        if($event->getSign()->getText()->getLines()[0] == "shop"){
            if(PSMain::getInstance()->isRegisterPShop($player->getName())){
                if(!PSMain::getInstance()->isPlaceSign($player->getName())){
                    $event->setNewText(new SignText([
                        TextFormat::AQUA . $player->getName(),
                        TextFormat::RED . "Shop's",
                        TextFormat::GOLD . "Buy to",
                        TextFormat::GOLD . "Click"
                    ]));
                    $player->sendMessage(TextFormat::GREEN . "Your shop sign has been created.");
                    PSMain::getInstance()->saveShopSignData($player, $event->getBlock()->getPosition()->asPosition());
                }else{
                    $player->sendMessage(TextFormat::RED . "You already place a sign. Delete your sign from the menu to disable it.");
                }
            }else{
                $player->sendMessage(TextFormat::RED . "First register  the shop system.");
            }
        }
    }

    /**
     * @throws JsonException
     */
    public function onBreak(BlockBreakEvent $event){
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $sign = $player->getWorld()->getTile($block->getPosition());
        if($sign instanceof Sign){
            if(PSMain::getInstance()->isPShopSign($sign->getPosition())){
                if(PSMain::getInstance()->isPShopOwner(TextFormat::clean($sign->getText()[0]), $player->getName())){
                    PSMain::getInstance()->deleteShopSignData($player->getName());
                    $player->sendMessage(TextFormat::RED . "Your shop sign has been deleted.");
                }else{
                    $player->sendMessage(TextFormat::RED . "You can't break someone else's shop sign.");
                    $event->cancel();
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event){
        if($event->getAction() != PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            $player = $event->getPlayer();
            $block = $event->getBlock();
            $sign = $player->getWorld()->getTile($block->getPosition());
            if($sign instanceof Sign){
                if(PSMain::getInstance()->isPShopSign($sign->getPosition())){
                    if(PSMain::getInstance()->isRegisterPShop($player->getName())){
                        if(!PSMain::getInstance()->isPShopOwner(TextFormat::clean($sign->getText()[0]), $player->getName())){
                            $player->sendForm(new PSOpenShopForm($player, PSMain::getInstance()->getSignOwner($sign->getPosition())));
                        }else{
                            $player->sendMessage(TextFormat::RED . "You can't open to own shop.");
                        }
                    }else{
                        $player->sendMessage(TextFormat::RED . "First register the shop system.");
                    }
                }
            }
        }
    }
}