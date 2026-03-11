# jobijoba-test

Suivre les commandes suivantes pour lancer le projet :

```
git clone https://github.com/Atyos35/jobijoba-test
cd jobijoba-test
docker compose up -d
docker exec -it app_apache sh
composer install
Se rendre sur : http://localhost/job/

Pour vérifier que les clés de caching sont actives : 
docker exec -it app_redis redis-cli keys "*"

```
