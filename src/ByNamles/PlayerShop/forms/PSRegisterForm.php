<?php


namespace ByNamles\PlayerShop\forms;

use ByNamles\PlayerShop\PSMain;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PSRegisterForm extends CustomForm{

    public function __construct(){
        parent::__construct(TextFormat::GOLD . "Shop Register Form", [
            new Input("element0",TextFormat::AQUA . "Your Name: ", TextFormat::DARK_GRAY . "Write your name."),
            new Input("element1",TextFormat::AQUA . "Age: ", TextFormat::DARK_GRAY . "Write your age."),
        ], function (Player $player, CustomFormResponse $response): void {
            $name = $response->getString("element0");
            $age = $response->getString("element1");
            if(strlen($name) > 1 && strlen($age) > 0){
                if(is_numeric($age)){
                    PSMain::getInstance()->registerPShop($player->getName(), $name, $age);
                    $player->sendMessage(TextFormat::GREEN . "You have successfully registered in the shop system.");
                }else{
                    $player->sendMessage(TextFormat::RED . "Age should be numeric, name shouldn't be numeric.");
                }
            }else{
                $player->sendMessage(TextFormat::RED . "Fill required blanks.");
            }
        });
    }
}