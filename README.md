
# LOGIZTIK DOCKER

Este es el proyecto dockerizado de logizitik para montarlo en local y luego subirlo a la nube.


## Como arrancar

Instalar docker Desktop desde su pagina oficial.
[DESCARGAR DOCKER](https://www.docker.com/products/docker-desktop/)
luego correr el comando:
```bash
docker-compose up -d
```
Abrir en el navegador: 
[Servidor Local](http://localhost:8000/)
## Instalar la base de datos

El proyecto viene con el respaldo de la base de datos (carpeta mysql) que debes importar usando phpmyadmin de preferencia.
[PhpMyAdmin](http://localhost:8082)

## Detener el proyecto

Es recomendable usar Docker Desktop para detener el proyecto, pero también puedes hacerlo por consola con: 

```bash
docker stop $(docker ps -q)
```


