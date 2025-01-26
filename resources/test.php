<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Bars with clip-path</title>
    <style>
        .progress-bar {
            width: 300px;
            height: 30px;
            background-color: #e0e0e0; /* Couleur de fond */
            position: relative;
            background-image: linear-gradient(to right, #3498db, #3498db); /* Couleur de remplissage */
            clip-path: inset(0 calc(100% - 0%) 0 0); /* Par défaut : aucun remplissage */
            transition: clip-path 0.5s ease; /* Animation fluide */
            margin-bottom: 10px; /* Espacement entre les barres */
        }
    </style>
</head>
<body>
<div class="progress-bar" data-fill="75"></div>
<div class="progress-bar" data-fill="50"></div>
<div class="progress-bar" data-fill="25"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('.progress-bar');

        progressBars.forEach(bar => {
            const fillPercentage = bar.getAttribute('data-fill'); // Récupérer le pourcentage
            const clipValue = 100 - fillPercentage; // Calcul pour le clip-path
            bar.style.clipPath = `inset(0 calc(${clipValue}% - 0) 0 0)`; // Mise à jour
        });
    });
</script>
</body>
</html>
