### Fonction pour afficher la liste des admin(1)-users(2)-god(3)
SELECT users.nom
FROM users
JOIN role ON users.id= role.id
WHERE role.id = 3;
### Fonction pour encoder un nouveau client 
insert into client 
(nom,email) 
values ('Lagauffre','jl@gd');
### Fonction pour encoder un nouveau rdv
insert into rdv
(Nbr_pers,description,date_rdv,heure_debut,heure_fin,id_1,id_2) 
values ('cours hype',4,23-11-2024,'13:00','15:00',12,2);
### Fonction pour encoder/modifier un poney
insert into PONEY (nom, tps_w,id_1) values
('Nuage',0,1)
### Fonction pour attribuer un cheval à un rdv
insert into preste 
(id,id_1) values 
(3,1);

### Fonction pour vérifier le status d'un poney
SELECT  nom
FROM poney join status on poney.id_1=status.id
where libelle='mort';
### Fonction pour 
SELECT nom, description 
FROM poney JOIN preste JOIN rdv 
ON poney.id_1=preste.id AND rdv.id= preste.id_1 
WHERE poney.id=preste.id;
