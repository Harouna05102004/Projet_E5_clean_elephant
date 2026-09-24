// Fallback desktop pour les liens "tel:" : sur mobile, comportement natif (ouvre le composeur).
// Sur desktop (pas d'app d'appel), on copie le numéro et on affiche une notification bien visible.
document.addEventListener('DOMContentLoaded', function () {
    var telLinks = document.querySelectorAll('a[href^="tel:"]');
    var isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

    telLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (isMobile) return;

            e.preventDefault();
            var numero = link.getAttribute('href').replace('tel:', '').replace('+33', '0');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(numero).catch(function () {});
            }

            showCallToast(numero);
        });
    });

    function showCallToast(numero) {
        var existing = document.querySelector('.call-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'call-toast';
        toast.innerHTML =
            '<span class="call-toast-icon">📞</span>' +
            '<span>' +
                '<strong>' + numero + '</strong>' +
                '<span>Numéro copié — collez-le pour nous appeler</span>' +
            '</span>';
        document.body.appendChild(toast);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                toast.classList.add('visible');
            });
        });

        setTimeout(function () {
            toast.classList.remove('visible');
            setTimeout(function () { toast.remove(); }, 350);
        }, 5000);
    }
});
