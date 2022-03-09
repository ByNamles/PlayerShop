<?php


namespace ByNamles\PlayerShop;

use ByNamles\PlayerShop\command\PSCommand;
use ByNamles\PlayerShop\forms\PSMainForm;
use JetBrains\PhpStorm\Pure;
use JsonException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class PSMain extends PluginBase{

    /** @var PSMain */
    private static PSMain $api;
    /** @var Config */
    public Config $signData;

    public function onLoad() : void{
        self::$api = $this;
    }

    public function onEnable() : void{
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "PSData/");

        $this->signData = new Config($this->getDataFolder() . "signData.yml", Config::YAML);

        $this->getControlPlugins();

        $this->getServer()->getCommandMap()->register("shop", new PSCommand());

        $this->getServer()->getPluginManager()->registerEvents(new PSEventListener(), $this);
    }

    public function getControlPlugins() : void{
        if(!class_exists('onebone\economyapi\EconomyAPI')){
            $this->getLogger()->warning(TextFormat::RED . "Couldn't find EconomyAPI plugin. Plugin is being disabled.");
            Server::getInstance()->getPluginManager()->disablePlugin($this);
        }
    }

    public static function getInstance() : PSMain{
        return self::$api;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "ByNamles"){
            if($sender instanceof Player){
                $sender->sendForm(new PSMainForm());
            }else{
                $sender->sendMessage(TextFormat::RED . "Lütfen bu komutu oyun içerisinde kullanın.");
            }
        }
        return true;
    }

    #[Pure] public function isRegisterPShop(string $player) : bool{
        return file_exists($this->getDataFolder() . "PSData/" . $player . ".yml");
    }

    /**
     * @throws JsonException
     */
    public function registerPShop(string $player, string $name, int $age) : void{
        $ps = new Config($this->getDataFolder() . "PSData/" . $player . ".yml", Config::YAML);
        $ps->set("Name", $name);
        $ps->set("Age", $age);
        $ps->set("Storage", []);
        $ps->save();
    }

    public function getPSData(string $player) : Config{
        return new Config($this->getDataFolder() . "PSData/" . $player .".yml", Config::YAML);
    }

    public function listStorage(string $player) : string{
        $list = "";
        $i = 0;
        foreach($this->getPSData($player)->get("Storage") as $key => $value){
            $itemData = explode(":", $key);
            $item = ItemFactory::getInstance()->get((int)$itemData[0], (int)$itemData[1], (int)$value);
            $list .= TextFormat::GRAY . "[" . TextFormat::WHITE . $i . TextFormat::GRAY . "]" . TextFormat::GREEN ." Item: " . TextFormat::RED . $item->getName() . TextFormat::GREEN . " Count: " . TextFormat::RED . $item->getCount() . TextFormat::GREEN . " Price: " . TextFormat::RED . $itemData[2] . PHP_EOL;
            $i++;
        }
        return $list;
    }

    public function getOptionsList(string $player) : array{
        $list = [];
        $i = 0;
        foreach($this->getPSData($player)->get("Storage") as $key => $value){
            $list[$i++] = $key . "-" . $value;
        }
        return $list;
    }

    /**
     * @throws JsonException
     */
    public function removeItemToPlayer(Item $item, string $playerName, int $count, int $price) : void{
        $ps = $this->getPSData($playerName);
        $storageData = $ps->get("Storage");
        $hash = $item->getId() . ":" . $item->getMeta() . ":" . $price;
        if($storageData[$hash] - $count >= 1){
            $setCount = $storageData[$hash] - $count;
            $storageData[$hash] = $setCount;
            $ps->set("Storage", $storageData);
            $ps->save();
        }else{
            unset($storageData[$hash]);
            $ps->set("Storage", $storageData);
            $ps->save();
        }
    }

    /**
     * @throws JsonException
     */
    public function addItemToPlayer(Item $item, string $playerName, int $price, int $count) : void{
        $ps = $this->getPSData($playerName);
        $storage = $ps->get("Storage") ?? [];
        $hash = $item->getId() . ":" . $item->getMeta() . ":" . $price;
        $this->getItemControl($hash, $playerName) ? $storage[$hash] = $count + $storage[$hash] : $storage[$hash] = $count;
        $ps->set("Storage", $storage);
        $ps->save();
    }

    public function getItemControl(string $hash, string $playerName) : bool{
        return isset($this->getPSData($playerName)->get("Storage")[$hash]);
    }

    public function isPlaceSign(string $player) : bool{
        return $this->signData->exists($player);
    }

    public function isPShopSign(Position $position) : bool{
        $signData = $this->signData->getAll();
        $pos = $position->getFloor();
        $hash = $pos->x . ":" . $pos->y . ":" . $pos->z . ":" . $position->getWorld()->getFolderName();
        $array = [];
        foreach($signData as $signDatum => $datum){
            $array[] = $datum;
        }
        return in_array($hash, $array);
    }

    public function isPShopOwner(string $text, string $owner) : bool{
        return $owner == $text;
    }

    /**
     * @throws JsonException
     */
    public function saveShopSignData(Player $player, Position $position) : void{
        $pos = $position->getFloor();
        $hash = $pos->x . ":" . $pos->y . ":" . $pos->z . ":" . $position->getWorld()->getFolderName();
        $this->signData->set($player->getName(), $hash);
        $this->signData->save();
    }

    /**
     * @throws JsonException
     */
    public function deleteShopSignData(string $player) : void{
        $this->signData->remove($player);
        $this->signData->save();
    }

    public function getSignOwner(Position $position) : string{
        $pos = $position->getFloor();
        $hash = $pos->x . ":" . $pos->y . ":" . $pos->z . ":" . $position->getWorld()->getFolderName();
        $array = [];
        foreach($this->signData->getAll() as $signDatum => $datum){
            $array[$datum] = $signDatum;
        }
        return $array[$hash];
    }

    public function getControlCount(string $player, string $hash, int $count) : bool{
        $ps = $this->getPSData($player);
        $totalCount = $ps->get("Storage")[$hash];
        return $totalCount >= $count;
    }
}