# Steps to start the whole environment

1. add 'techtest.local' to your windows virtualhosts ( C:\Windows\System32\drivers\etc\hosts ) 

  ...
  127.0.0.1    techtest.local


2. run 'docker compose -f ./docker-compose.prod.yaml up -d --force-recreate --build' in this folder
