// Empêche la saisie de chiffres dans les champs "nom" et "prénom" (tous les formulaires du site).
document.addEventListener('DOMContentLoaded', function () {
    var champsTexteSeulement = document.querySelectorAll('input[name="prenom"], input[name="nom"]');

    champsTexteSeulement.forEach(function (champ) {
        // Bloque la frappe d'un chiffre au clavier
        champ.addEventListener('keypress', function (e) {
            if (/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Filet de sécurité : nettoie les chiffres venant d'un copier-coller,
        // de l'autocomplétion du navigateur ou d'un glisser-déposer
        champ.addEventListener('input', function () {
            var positionCurseur = champ.selectionStart;
            var valeurAvant = champ.value;
            var valeurSansChiffres = valeurAvant.replace(/[0-9]/g, '');

            if (valeurSansChiffres !== valeurAvant) {
                var chiffresSupprimesAvantCurseur = (valeurAvant.slice(0, positionCurseur).match(/[0-9]/g) || []).length;
                champ.value = valeurSansChiffres;
                var nouvellePosition = Math.max(0, positionCurseur - chiffresSupprimesAvantCurseur);
                champ.setSelectionRange(nouvellePosition, nouvellePosition);
            }
        });
    });
});
