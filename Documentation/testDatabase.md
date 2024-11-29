## Insert data
<details>

<summary>Code SQLITE3</summary>

Insertion des données
```ruby
insert into CLIENT (nom,Nbr_pers,email,updated_at,created_at) values 
('Salmon', 4,'salmon@yahoo.fr',current_time,current_time),
('Scheepens', 2,'bit@gmail.com',current_time,current_time),
('Bemont', 10,'bemont56@yahoo.fr',current_time,current_time),
('Gérard', 5,'gege75@yahoo.fr',current_time,current_time),
('De Smet', 2,'smet34@yahoo.fr',current_time,current_time),
('Colpin', 4,'colpinphil@gmail.com',current_time,current_time),
('Stasse', 6,'stassesisters@yahoo.be',current_time,current_time),
('Buisseret', 7,'buiss@cool.be',current_time,current_time),
('Morelle', 3,'momorerelele@orange.net',current_time,current_time),
('De Potter', 2,'depotterharry@gmail.be',current_time,current_time),
('Transient', 1,'trans@cool.be',current_time,current_time)
;

insert into ROLE values
(1,'admin',current_time,current_time),
(2,'empl',current_time,current_time),
(3,'god',current_time,current_time)
;

insert into STATUS (id,libelle, updated_at,created_at) values 
(1,'disponible',current_time,current_time),
(2,'repos',current_time,current_time),
(3,'mort',current_time,current_time),
(4,'malade',current_time,current_time)
;

insert into PONEY (nom, tps_w, updated_at,created_at,id_1) values 
('Nuage',0,current_time,current_time,1),
('Câlin',0,current_time,current_time,1),
('Éclair',0,current_time,current_time,1),
('Zéphir',0,current_time,current_time,1),
('Plume',0,current_time,current_time,1),
('Caramel',0,current_time,current_time,1),
('Galaxy',0,current_time,current_time,1),
('Praline',0,current_time,current_time,1),
('Saphir',0,current_time,current_time,1)
;

insert into USERS (nom, prenom,email, updated_at,created_at,id_1) values
('Golenvaux','Nicolas','nicolasgoenvaux@gmail.com',current_time, current_time,3),
('Ferauche','Marie','marie.fe@yahoo.com',current_time, current_time,2),
('Godefroid','Bill','godbill@gmail.com',current_time, current_time,2),
('Berlusconi','Josiane','jojo@gmail.com',current_time, current_time,1);

insert into rdv (description, Nbr_pers,date_rdv,heure_debut,heure_fin, updated_at,created_at,id_1,id_2) values
('stage essai', 2,'01-11-2024','13:00:00','14:00:00',current_time, current_time,2,1),
('stage niveau 3',4, '05-11-2024','13:00:00','14:00:00',current_time, current_time,1,2),
('stage essai',5, '02-11-2024','13:00:00','14:00:00',current_time, current_time,3,1);

```

</details>


