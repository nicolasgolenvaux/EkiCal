## Installation de SQLite 3
- Téléchargement des fichiers  
  - https://www.sqlite.org/download.html 
  - Precompiled Binaries for Windows ``sqlite-dll-win-x86-xxxxxxxx.zip`` + ``sqlite-tools-win-x64-xxxxxxxx.zip``
  - Création du dossier sqlite à la racine
  - Décompression des fichiers temporaires dans le dossier sqlite
- Variable d'environnement  
  - Ajout de la variable C:\sqlite 
  - ``systeme`` -> ``variables d'environnement`` -> ``Path`` -> ``C:\sqlite``

## Génération de la base de donnée

- Ouvrir une invite de commande
- Créer un dossier de destination de la base de donnée
- Se rendre dans ce dossier
- Ouverture SQLite ``sqlite3``


## Génération des tables de la Base de données
<details>

<summary>Code EkiCal 2.0</summary>

Création des tables 2.0
```ruby

   CREATE TABLE CLIENT(
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   nom VARCHAR(50) NOT NULL,
   email VARCHAR(50) NOT NULL,
   updated_at DATETIME default current_time,
   created_at DATETIME default current_time
);

CREATE TABLE FACTURE(
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   dateFact DATE NOT NULL,
   created_at DATETIME default current_time,
   updated_at DATETIME default current_time,
   id_1 INT NOT NULL,
   FOREIGN KEY(id_1) REFERENCES CLIENT(id)
);




CREATE TABLE users(
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   name VARCHAR(50) NOT NULL,
   email VARCHAR(150),
   role VARCHAR(50) default 'user',
   updated_at DATETIME default current_time,
   created_at DATETIME default current_time,
   image_path VARCHAR(150),
   password VARCHAR(150) NOT NULL
);

CREATE TABLE poneys(
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      nom VARCHAR(50) NOT NULL,
                      tps_w TIME,
                      updated_at DATETIME default current_time,
                      created_at DATETIME default current_time,
                      image_path VARCHAR(50),
                      weight int,
                      birth DATETIME,
                      medicalVisit DATETIME
);

CREATE TABLE RDV(
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   Nbr_pers INT NOT NULL,
   description VARCHAR(50) NOT NULL,
   date_rdv DATE NOT NULL,
   heure_debut TIME NOT NULL,
   heure_fin TIME NOT NULL,
   updated_at DATETIME default current_time,
   created_at DATETIME default current_time,
   id_1 INT NOT NULL,
   id_2 INT NOT NULL,
   FOREIGN KEY(id_2) REFERENCES USERS(id)
   FOREIGN KEY(id_1) REFERENCES CLIENT(id)
);

CREATE TABLE preste(
   id INT,
   id_1 INT,
   updated_at DATETIME default current_time,
   created_at DATETIME default current_time,
   PRIMARY KEY(id, id_1),
   FOREIGN KEY(id) REFERENCES PONEY(id),
   FOREIGN KEY(id_1) REFERENCES RDV(id)
);

CREATE TABLE enregistre(
   id INT,
   id_1 INT,
   prix CURRENCY NOT NULL,
   quantité INT NOT NULL,
   updated_at DATETIME default current_time,
   created_at DATETIME default current_time,
   PRIMARY KEY(id, id_1),
   FOREIGN KEY(id) REFERENCES FACTURE(id),
   FOREIGN KEY(id_1) REFERENCES RDV(id)
);

```
Sauvegarde de la base de donnée ``.save ekical.db`` 
Pour activer la contrainte d'intégrité  
``PRAGMA foreign_keys = ON``
</details>

<details>

<summary>Code EkiCal 1.0</summary>

Création des tables 1.0
```ruby
   CREATE TABLE CLIENT(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
nom VARCHAR(50) NOT NULL,  
Nbr_pers BYTE NOT NULL,  
email VARCHAR(50) NOT NULL,  
updated_at DATETIME,  
created_at DATETIME,
);   
  
CREATE TABLE FACTURE(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
dateFact DATE NOT NULL,  
id_user int,  
id_client INT,  
created_at DATETIME,  
updated_at DATETIME,  
id_1 INT NOT NULL,
FOREIGN KEY(id_1) REFERENCES CLIENT(id)  
);  
   
CREATE TABLE ROLE(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
Libelle VARCHAR(50),  
created_at DATETIME,  
updated_at VARCHAR(50)
);  
  
CREATE TABLE STATUS(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
libelle VARCHAR(50) NOT NULL,  
updated_at DATETIME,  
created_at DATETIME
);  
  
CREATE TABLE RDV(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
description VARCHAR(50) NOT NULL,  
date_rdv DATE NOT NULL,  
heure_debut TIME NOT NULL,  
heure_fin TIME NOT NULL,  
updated_at DATETIME,  
created_at DATETIME
);  
  
CREATE TABLE USERS(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
nom VARCHAR(50) NOT NULL,  
prenom VARCHAR(50) NOT NULL,  
email VARCHAR(150),  
updated_at DATETIME,  
created_at DATETIME,  
id_role int NOT NULL,
FOREIGN KEY(id_role) REFERENCES ROLE(id_role)  
);  
  
CREATE TABLE PONEY(  
id INTEGER PRIMARY KEY AUTOINCREMENT,  
nom VARCHAR(50) NOT NULL,  
tps_w TIME,  
updated_at DATETIME,  
created_at VARCHAR(50),  
id_1 INT NOT NULL,
FOREIGN KEY(id_1) REFERENCES STATUS(id)  
);  
  
CREATE TABLE preste(  
id INT,  
id_1 INT,  
updated_at DATETIME,  
created_at DATETIME,  
PRIMARY KEY(id, id_1),  
FOREIGN KEY(id) REFERENCES PONEY(id),  
FOREIGN KEY(id_1) REFERENCES RDV(id)  
);  
  
CREATE TABLE encode(  
id INT,  
id_1 INT,  
updated_at DATETIME,  
created_at DATETIME,  
PRIMARY KEY(id, id_1),  
FOREIGN KEY(id) REFERENCES FACTURE(id),  
FOREIGN KEY(id_1) REFERENCES USERS(id)  
);  
  
CREATE TABLE enregistre(  
id INT,  
id_1 INT,  
prix CURRENCY NOT NULL,  
quantité INT NOT NULL,  
updated_at DATETIME,    
created_at DATETIME,  
PRIMARY KEY(id, id_1),  
FOREIGN KEY(id) REFERENCES FACTURE(id),  
FOREIGN KEY(id_1) REFERENCES RDV(id)  
);  
```
Sauvegarde de la base de donnée ``.save ekical.db``

</details>


## Vérifications

- ``.exit``
- ``sqlite3``
- ``.open ekical.db``
- ``.tables``

out :
``CLIENT      PONEY       ROLE        USERS       enregistre
FACTURE     RDV         STATUS      encode      preste``