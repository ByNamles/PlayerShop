<?php

namespace ByNamles\PlayerShop\forms\textures;

use dktapps\pmforms\FormIcon;
use JetBrains\PhpStorm\Pure;

class PSButtonImage{
    #[Pure] public static function getItemPNG(int $id, int $damage) : FormIcon{
        return new FormIcon("https://avengetech.me/items/" . $id . "-" . $damage . ".png", FormIcon::IMAGE_TYPE_URL);
    }
}