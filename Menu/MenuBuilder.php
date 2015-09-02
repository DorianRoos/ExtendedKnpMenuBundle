<?php
/**
 * Created by PhpStorm.
 * User: roos
 * Date: 24/08/15
 * Time: 13:26
 */

namespace DorianRoos\Symfony\ExtendedKnpMenuBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /** @var FactoryInterface  */
    private $factory;

    /** @var AuthorizationChecker */
    private $authorizationChecker;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param FactoryInterface $factory
     * @param AuthorizationChecker $authorizationChecker
     * @param TranslatorInterface $translator
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->translator = $translator;
    }

    /**
     * @param $menuData Array
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu($menuData)
    {
        if(!is_array($menuData) || empty($menuData)) {
            throw new \InvalidArgumentException(sprintf('You have to provide a valid array as parameters'));
        }

        $contents = array();
        !isset($menuData["contents"]) ?: $contents = $menuData["contents"];

        /** @var \Knp\Menu\ItemInterface $menu */
        $menu = $this->factory->createItem('root');

        $this->addMenuElement($contents, $menu);

        return $menu;
    }

    /**
     * @param $contents Array
     * @param $menu \Knp\Menu\ItemInterface
     */
    public function addMenuElement($contents, $menu){
        !isset($contents["text"]) ?: $contents = array($contents);

        foreach($contents as $identifier => $menuItem){
            /** check if they are role parameters ( default : IS_AUTHENTICATED_ANONYMOUSLY ) */
            isset($menuItem['role']) ? $role = $menuItem['role'] : $role = 'IS_AUTHENTICATED_ANONYMOUSLY' ;

            /** Check if use full text or id trad ( default : false ) */
            (isset($menuItem['textMode']) && $menuItem['textMode'] == true)
                ? $text = $menuItem['text']
                : $text = $this->translator->trans($menuItem['text']) ;

            /** If user can display the item, whe add to the menu */
            if($this->authorizationChecker->isGranted($role)) {
                !isset($menuItem['route']) ?: $menu->addChild($identifier, array('route' => $menuItem['route'], 'label' => $text));
                (!isset($menuItem['uri']) && isset($menuItem['route'])) ?: $menu->addChild($identifier, array('uri' => $menuItem['uri'], 'label' => $text));

                if(isset($menuItem['submenu'])){
                    $this->addMenuElement($menuItem['submenu']['contents'],$menu[$identifier]);
                }
            }
        }
    }
}