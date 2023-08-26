# Changelog plugin Dreame

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 26/08/2023

- Refonte complète du plugin pour plus d'évolutions par la suite et plus de lisibilité
- Les robots de la marque Roborock sont maintenant supportés par le plugin
- Ajout d'une commande "Synchroniser les commandes" pour eviter de re-creer systematiquement toutes les commandes
- Roborock : Possibilité de recuperer les differentes pièces de la maison. 1 pièce = 1 commande, vous pourrez renommer ces commandes pour remplacer l'ID par le nom de la pièce. Vous pourrez ensuite lancer un nettoyage juste pour cette pièce
- Le plugin Dreame est maintenant intégrable dans Jeedom Connect (et d'ailleurs aussi dans JeeMate en respectant les generiques types)
- Bouton pour contacter rapidement le forum Community en cas de besoin (version Core au moins V4.4)
- Optimisations diverses
- Diverses corrections sur les textes et noms
- Ajout enfin d'une icône pour le plugin Dreame et pour les differents modèles de robots.

  Je tiens à remercier @Tomitomas pour ce changelog, intégralement effectué par ses soins et qui permet de re-dynamiser completement le plugin. Sur la forme le changelog est peut-être petit mais sur le fond il y a eu un très gros travail et quasiment tout le plugin a été refait.

  # NB : Afin de faciliter la mise à jour, merci de supprimer vos équipements et de refaire une nouvelle detection.

# 22/08/2023

- Début du developpement pour integrer la marque Roborock sur le plugin.
- Roborock S8 fonctionnel avec le plugin 
- Correction lié à la detection des appareils
- Mise à jour automatique de l'adresse IP et du Token lorsqu'on detecte les nouveaux robots et que le robot est déjà présent
- Optimisations diverses
  
# 12/03/2023

- Correction d'une anomalie lors de la découverte des appareils qui remonte nulle.

# 12/03/2023

- Ajout de log lors de la découverte des nouveaux appareils
- Filtration des appareils pour ne ressortir que les aspirateurs robots 

# 11/03/2023

- Mise en place du système d'installation des dépendances sous Jeedom. (Compatible Debian 10 - 11)

# 02/03/2023

- Mise en route du changelog !!
