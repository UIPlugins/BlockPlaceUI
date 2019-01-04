<?php
/**
 * Created by PhpStorm.
 * User: tru
 * Date: 1/3/2019
 * Time: 11:29 PM
 */

namespace BlockPlaceUI;

use ARTulloss\libBoolUI\YesNoForm;
use pocketmine\event\{block\BlockPlaceEvent, Listener};
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class Main extends PluginBase implements Listener
{
    public static $lol = [];

    public function onEnable(): void
    {
        $this->getLogger()->info("Enabled");
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();

        if (!in_array($event->getPlayer()->getName(), self::$lol)){
            $event->setCancelled(true);

            $form = new YesNoForm(function(Player $player, $data){
                switch ($data) {
                    case 0:
                        $rand = rand(0, 500);
                        if ($rand === 5) {
                            array_push(self::$lol, $player->getName());
                        }
                        break;
                }
            });

            $form->randomize();
            $form->setForced();

            $form->registerButtons();

            $form->setTitle("BlockPlaceUI");
            $form->setContent("Are you sure you want to place this block?");

            $form->setImage($form::YES, false, "textures/ui/checkboxFilledYellow");
            $form->setImage($form::NO, false, "textures/ui/checkboxUnFilled");
            $player->sendForm($form);
        }
    }

    public function onDisable(): void
    {
        $this->getLogger()->info("Disabled");
    }
}
