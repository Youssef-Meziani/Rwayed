document.addEventListener('DOMContentLoaded', function () {
    var dateMaskElement = document.getElementById('pneu_taille');
    var lastValue = ''; // Garder une trace de la dernière valeur pour détecter des suppressions.

    var maskOptions = {
        mask: '{1-9}{0-9}{0-9}{/}00{R}00',
        blocks: {
            '1-9': { mask: IMask.MaskedRange, from: 1, to: 9, maxLength: 1 },
            '0-9': { mask: IMask.MaskedRange, from: 0, to: 9, maxLength: 1, optional: true },
            '00': { mask: IMask.MaskedRange, from: 10, to: 99, maxLength: 2 },
            'R': { mask: 'R' }
        },
        definitions: {
            '0-9': /[0-9]/
        },
        lazy: false // Rend les parties statiques du masque toujours visibles.
    };

    var mask = IMask(dateMaskElement, maskOptions);

    dateMaskElement.addEventListener('input', function (e) {
        var cursorPosition = dateMaskElement.selectionStart; // Obtenir la position actuelle du curseur.

        // Mettre à jour le masque si nécessaire, basé sur la logique dynamique.
        // Cela est géré par la configuration initiale du masque dans ce cas.

        // Si un caractère a été supprimé, ajustez la position du curseur.
        if (dateMaskElement.value.length < lastValue.length && cursorPosition > 0) {
            cursorPosition--;
        }

        // Restaurer la position du curseur après la mise à jour.
        dateMaskElement.setSelectionRange(cursorPosition, cursorPosition);

        lastValue = dateMaskElement.value; // Mettre à jour la dernière valeur connue.
    });
});