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

insert into USERS  values
                    
(1,Nicolas Golenvaux,nicolasgolenvaux@gmail.com,admin,1989,2025-01-01 12:59:10,uploads/67753c1ed13ff.PNG,$2y$10$KHVrlfVTRODfE4q4KufLL.vIyUhb3w0hStb7M.tgQqlZuOegSmfYK),
(2,Marie  Ferauche,marie.fe48@gmail.com,user,1984,2025-01-01 13:15:30,uploads/67753ff21ffee.jpg,$2y$10$A9Sign22bVxFdEGXU2kGFOiyI8GoT.JYHWQl41vjRBrvt/1a63.xS),
(3,Gérard Ernalsteen,gg@gmail.com,user,1983,2025-01-01 13:14:56,uploads/67753c4022787.jpg,$2y$10$pMRC2sn519zxxnPBXPmCAOgxyZUpB8/m4mmLXZbOPjXT4MfXHOb32),
(4,Laurence VVK,Louis@gmail.com,user,1983,2025-01-01 13:13:55,uploads/67753bd9d5258.jpg,$2y$10$BWQfw6/g8utbzUFyov5kP.2LVH2TWZgbFw9w/9DeP/DRptTTQ.9JC),
(5,Denayer Jean,jd@d.com,user,1983,2025-01-01 12:57:19,uploads/67753baeedc3e.jpg,$2y$10$kd.kvyUezyk1tjVIjPh/LO/TIc5pcwKJpeF88WdpeT2w0upyADbjy),
(6,Fabienne Lambini,paradis@eden.com,admin,1983,2025-01-01 13:13:41,uploads/67753b8f45bb7.jpg,$2y$10$9Ou2NMRkDqxmC6CtkN7pgeAsCRHEhY.70tamizqsJbHyWCIJr35gG),
(7,Carole Guyot,cagu@gmail.com,a,2025-01-01 13:55:39,2025-01-01 12:56:23,uploads/67753b77c6d6b.jpg,$2y$10$OGrXLEjWe28jLRXLS4piwuO/1BxlzcUK5fKTIQtsul/ZBkKVfTaHq)


insert into rdv (description, Nbr_pers,date_rdv,heure_debut,heure_fin, updated_at,created_at,id_1,id_2) values
('stage essai', 2,'01-11-2024','13:00:00','14:00:00',current_time, current_time,2,1),
('stage niveau 3',4, '05-11-2024','13:00:00','14:00:00',current_time, current_time,1,2),
('stage essai',5, '02-11-2024','13:00:00','14:00:00',current_time, current_time,3,1);

```

</details>


