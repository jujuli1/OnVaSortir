docker --version
docker info

lister image locales : docker images

dl une image :

docker pullnginx
docker pull MySQL:8

supprimmer image : docker rmi Nginx

lister conteneurs: docker ps

lancer un conteneur avec nom + port
docker run -d --name web -p 8080:80 Nginx

arreter/demarrer conteneur:
docker stop mon_projet
docker start mon_projet
docker restart mon_projet

entrer terminal conteneur:
docker exec -it mon_projet_php bash // docker exec -it mon_projet_php sh

log en temps réel : docker logs -f mon_conteneur


sauvegarde image : docker save -o image.tar Nginx

docker-compose up -d : lit le dossier docker-compose.yml
docker exec -it mon_projet_php bash: entrer dans le containers
docker exec -it mon_projet_db mysql -u root -p entrer dans la bdd avec root comme mdp



Lancer test ui avec playwright:
npx playwright test --ui

Depuis le conteneur Docker PHP --> lancer le test de supression en cascade
docker compose exec php php bin/phpunit tests/Entity/UtilisateurCascadeTest.php
