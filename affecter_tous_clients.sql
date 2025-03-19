USE BUDGET;

-- Récupérer l'ID de And1
SET @conseiller_id = (SELECT ID_Utilisateur FROM UTILISATEURS WHERE Email = 'and1@gmail.com');

-- Affecter tous les clients (sauf And1 lui-même) au conseiller
UPDATE UTILISATEURS 
SET ID_Conseiller = @conseiller_id
WHERE Role = 'client' 
AND Email != 'and1@gmail.com';
