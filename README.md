ExtendedKnpMenuBundle
================

Pour créer un menu, la configuration est la même que pour le knpmenubundle normal à quelques subtilités près.

#### Exemple :
    services:
      menu.main_menu:
        class: Knp\Menu\MenuItem # the service definition requires setting the class
        factory: ["@extended_knp_menu.menu_builder", createMenu]
        arguments: [%menu.main%]
        tags:
            - { name: knp_menu.menu, alias: main } # The alias is what is used to retrieve the menu
            
Le paramètre factory ne change pas, on passe dans arguments les paramètres du menu à afficher.

Structure des paramètres pour un menu:

    parameters:
      {menu-identifier}:
        contents:
          {element-identifier}
            {parameter}: {value}
            submenu:
              contents:
                {element-identifier}
                  {parameter}: {value}
                  
### Liste des paramètres

Obligatoires :

    - text ( Full text ou clé de traduction )
    - route
    - uri

Optionnels :

    - textMode ( par defaut : false )
        Permet de savoir si on affiche le texte brut ou si il s'agit d'une clé de traduction.
    - role ( par defaut : IS_AUTHENTICATED_ANONYMOUSLY )
        Définis le role utilisateur qui pour voir le menu.
    
#### Exemple :
    parameters:
      menu.main:
        contents:
          home:
            textMode: true
            text: "Acceuil"
            route: homepage
            submenu:
              contents:
                pomme:
                  textMode: false
                  text: "pomme.text"
                  route: homepage
          salade:
            textMode: true
            text: "Salade"
            uri: "http://www.salade.fr"
          google:
            textMode: true
            text: "Google"
            uri: "http://www.google.fr"
            
### Attributs :

Pour chaque Item il est possible de lui donner des attributs html.

Il existe 3 types d'attributs: attributes, linkAttributes, childrenAttributes

#### Exemple :
    bundles:
      textMode: true
      text: "Bundles"
      uri: "#"
      linkAttributes:
        data-toggle: "dropdown"
        role: "button"
        aria-haspopup: "true"
        aria-expanded: "false"
            
### Afficher le menu
          
Dans un template twig il suffit d'utilisé la méthode knp_menu_render().

#### Exemple :
    {{ knp_menu_render('main') }}