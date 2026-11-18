/*
 * js/main.js : Interactions du site Clean Elephant (JavaScript vanilla, sans bibliothèque)
 *
 * 1. En-tête qui se réduit au défilement
 * 2. Menu mobile (bouton hamburger)
 * 3. Sous-menu « Nos services »
 * 4. Comparateur Avant / Après
 * 5. Compteurs animés
 * 6. Apparition des blocs au défilement
 * 7. Reflet qui suit la souris sur la photo du hero
 * 8. Démonstration automatique du comparateur
 * 9. Carte OpenStreetMap chargée au clic (RGPD)
 * 10. Filtre « Vous êtes » du formulaire de devis
 * 11. Vidéo de démonstration (lecture seulement quand elle est visible)
 *
 * Le script est chargé avec « defer » : il s'exécute quand le HTML est prêt.
 * Tout est enveloppé dans une fonction pour ne créer aucune variable globale.
 */
(function () {
  'use strict';

  // L'utilisateur a-t-il demandé à son système de réduire les animations ?
  var reduireAnimations = window.matchMedia('(prefers-reduced-motion: reduce)').matches;


  /* ---------------------------------------------------------------
     1. EN-TÊTE QUI SE RÉDUIT + BARRE DE PROGRESSION
     Au-delà de 20px de défilement, on ajoute .is-scrolled : le CSS
     réduit la hauteur de l'en-tête et ajoute une ombre.
     --progress (de 0 à 1) règle la largeur de la barre dorée.
     requestAnimationFrame : au plus un calcul par image affichée,
     même si l'événement scroll se déclenche des dizaines de fois.
     --------------------------------------------------------------- */
  var header = document.getElementById('site-header');
  if (header) {
    var calculPrevu = false;
    var majHeader = function () {
      calculPrevu = false;
      header.classList.toggle('is-scrolled', window.scrollY > 20);
      var max = document.documentElement.scrollHeight - window.innerHeight;
      header.style.setProperty('--progress', max > 0 ? (window.scrollY / max).toFixed(4) : 0);
    };
    majHeader();
    // passive: true -> indique au navigateur qu'on ne bloque pas le défilement (plus fluide)
    window.addEventListener('scroll', function () {
      if (!calculPrevu) {
        calculPrevu = true;
        requestAnimationFrame(majHeader);
      }
    }, { passive: true });
  }


  /* ---------------------------------------------------------------
     2. MENU MOBILE
     aria-expanded informe les lecteurs d'écran de l'état du menu ;
     l'attribut hidden affiche / cache le panneau.
     --------------------------------------------------------------- */
  var burger = document.querySelector('.burger');
  var drawer = document.getElementById('menu-mobile');

  function fermerMenu() {
    if (!burger || !drawer) return;
    burger.setAttribute('aria-expanded', 'false');
    burger.setAttribute('aria-label', 'Ouvrir le menu');
    drawer.hidden = true;
    document.body.classList.remove('menu-ouvert');
  }

  if (burger && drawer) {
    burger.addEventListener('click', function () {
      var ouvert = burger.getAttribute('aria-expanded') === 'true';
      if (ouvert) {
        fermerMenu();
      } else {
        burger.setAttribute('aria-expanded', 'true');
        burger.setAttribute('aria-label', 'Fermer le menu');
        drawer.hidden = false;
        document.body.classList.add('menu-ouvert');
      }
    });

    // Un clic sur un lien du menu le referme (utile pour les ancres comme #zone)
    drawer.addEventListener('click', function (evt) {
      if (evt.target.closest('a')) fermerMenu();
    });
  }


  /* ---------------------------------------------------------------
     3. SOUS-MENU « NOS SERVICES »
     Au survol, c'est le CSS qui l'ouvre. Ce code gère le clic et le
     clavier (Entrée / Espace sur le bouton), et la touche Échap.
     --------------------------------------------------------------- */
  var boutonsSousMenu = document.querySelectorAll('.nav__toggle-sub');

  boutonsSousMenu.forEach(function (bouton) {
    bouton.addEventListener('click', function () {
      var ouvert = bouton.getAttribute('aria-expanded') === 'true';
      bouton.setAttribute('aria-expanded', String(!ouvert));
    });
  });

  // Clic ailleurs sur la page : on referme les sous-menus
  document.addEventListener('click', function (evt) {
    boutonsSousMenu.forEach(function (bouton) {
      if (!bouton.parentElement.contains(evt.target)) {
        bouton.setAttribute('aria-expanded', 'false');
      }
    });
  });

  // Touche Échap : ferme tout (sous-menus et menu mobile)
  document.addEventListener('keydown', function (evt) {
    if (evt.key !== 'Escape') return;
    boutonsSousMenu.forEach(function (bouton) { bouton.setAttribute('aria-expanded', 'false'); });
    if (burger && burger.getAttribute('aria-expanded') === 'true') {
      fermerMenu();
      burger.focus();   // on rend le focus au bouton pour ne pas « perdre » l'utilisateur clavier
    }
  });


  /* ---------------------------------------------------------------
     4. COMPARATEUR AVANT / APRÈS
     L'<input type="range"> invisible gère déjà souris, doigt et clavier.
     On recopie simplement sa valeur dans la variable CSS --pos.
     --------------------------------------------------------------- */
  document.querySelectorAll('.comparison-slider').forEach(function (slider) {
    var range = slider.querySelector('.comparison-slider__range');
    if (!range) return;

    var majPosition = function () {
      slider.style.setProperty('--pos', range.value + '%');
    };
    range.addEventListener('input', majPosition);
    majPosition();
  });


  /* ---------------------------------------------------------------
     5. COMPTEURS ANIMÉS
     IntersectionObserver prévient quand un élément entre à l'écran :
     on anime alors le chiffre de 0 jusqu'à data-count-target.
     --------------------------------------------------------------- */
  var compteurs = document.querySelectorAll('[data-count-target]');

  function animerCompteur(element) {
    var cible = parseInt(element.getAttribute('data-count-target'), 10);
    var duree = 1400;          // millisecondes
    var debut = null;

    function etape(horodatage) {
      if (debut === null) debut = horodatage;
      var progression = Math.min((horodatage - debut) / duree, 1);
      // Courbe « ease-out » : rapide au début, ralentit à la fin
      var adouci = 1 - Math.pow(1 - progression, 3);
      element.textContent = Math.round(cible * adouci);
      if (progression < 1) requestAnimationFrame(etape);
    }
    requestAnimationFrame(etape);
  }

  if (compteurs.length && 'IntersectionObserver' in window && !reduireAnimations) {
    var observateurCompteurs = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (entree.isIntersecting) {
          animerCompteur(entree.target);
          observateurCompteurs.unobserve(entree.target);   // une seule fois
        }
      });
    }, { threshold: 0.6 });

    compteurs.forEach(function (compteur) {
      compteur.textContent = '0';
      observateurCompteurs.observe(compteur);
    });
  }
  // Sinon (vieux navigateur ou animations réduites) : les vrais chiffres restent affichés.


  /* ---------------------------------------------------------------
     6. APPARITION AU DÉFILEMENT
     On ne cache QUE les blocs encore sous l'écran : le haut de la page
     s'affiche instantanément, et sans JavaScript rien n'est caché.
     --------------------------------------------------------------- */
  var blocs = document.querySelectorAll('[data-reveal]');

  if (blocs.length && 'IntersectionObserver' in window && !reduireAnimations) {
    /*
     * Un élément « wipe » est entièrement masqué par clip-path au départ :
     * le navigateur le considère alors comme jamais visible. On surveille
     * donc son PARENT (toujours visible), et on révèle ses enfants d'un coup.
     * La Map associe chaque élément surveillé à la liste des blocs à révéler.
     */
    var aReveler = new Map();

    var observateurBlocs = new IntersectionObserver(function (entrees) {
      entrees.forEach(function (entree) {
        if (entree.isIntersecting) {
          aReveler.get(entree.target).forEach(function (bloc) {
            bloc.classList.add('is-revealed');
            bloc.classList.remove('reveal-pending');
          });
          observateurBlocs.unobserve(entree.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    blocs.forEach(function (bloc) {
      var rect = bloc.getBoundingClientRect();
      if (rect.top > window.innerHeight) {
        bloc.classList.add('reveal-pending');
        // Décalage selon la position de l'élément parmi ses voisins (effet « cascade »)
        var rang = Array.prototype.indexOf.call(bloc.parentElement.children, bloc);
        bloc.style.transitionDelay = (rang % 4) * 120 + 'ms';

        var cible = bloc.getAttribute('data-reveal') === 'wipe' ? bloc.parentElement : bloc;
        if (!aReveler.has(cible)) {
          aReveler.set(cible, []);
          observateurBlocs.observe(cible);
        }
        aReveler.get(cible).push(bloc);
      }
    });
  }


  /* ---------------------------------------------------------------
     7. REFLET QUI SUIT LA SOURIS
     On envoie la position de la souris au CSS (--mx, --my) : un dégradé
     lumineux se place sous le curseur, comme un reflet sur une vitre.
     Seulement sur les appareils avec une souris (hover: hover).
     --------------------------------------------------------------- */
  if (window.matchMedia('(hover: hover)').matches && !reduireAnimations) {
    document.querySelectorAll('.hero__frame').forEach(function (cadre) {
      cadre.addEventListener('pointermove', function (evt) {
        var r = cadre.getBoundingClientRect();
        cadre.style.setProperty('--mx', ((evt.clientX - r.left) / r.width * 100).toFixed(1) + '%');
        cadre.style.setProperty('--my', ((evt.clientY - r.top) / r.height * 100).toFixed(1) + '%');
      });
    });
  }


  /* ---------------------------------------------------------------
     8. DÉMONSTRATION DU COMPARATEUR
     Quand le comparateur apparaît, la poignée fait un aller-retour
     tout seul pour montrer au visiteur qu'il peut la déplacer.
     Si le visiteur touche le curseur, la démo s'arrête aussitôt.
     --------------------------------------------------------------- */
  if ('IntersectionObserver' in window && !reduireAnimations) {
    document.querySelectorAll('.comparison-slider[data-demo]').forEach(function (slider) {
      var range = slider.querySelector('.comparison-slider__range');
      if (!range) return;

      var interrompu = false;
      ['pointerdown', 'keydown', 'focus'].forEach(function (type) {
        range.addEventListener(type, function () { interrompu = true; });
      });

      function placer(valeur) {
        range.value = valeur;
        slider.style.setProperty('--pos', valeur + '%');
      }

      function lancerDemo() {
        var etapes = [50, 78, 22, 50];   // positions successives de la poignée (en %)
        var duree = 750;                // durée de chaque déplacement (ms)
        var i = 0;

        function deplacement() {
          if (interrompu || i >= etapes.length - 1) return;
          var depart = etapes[i];
          var arrivee = etapes[i + 1];
          var t0 = null;

          function image(t) {
            if (interrompu) return;
            if (t0 === null) t0 = t;
            var p = Math.min((t - t0) / duree, 1);
            // Courbe « ease-in-out » : démarrage et arrêt en douceur
            var adouci = p < 0.5 ? 2 * p * p : 1 - Math.pow(-2 * p + 2, 2) / 2;
            placer((depart + (arrivee - depart) * adouci).toFixed(1));
            if (p < 1) {
              requestAnimationFrame(image);
            } else {
              i++;
              deplacement();
            }
          }
          requestAnimationFrame(image);
        }
        deplacement();
      }

      var observateurDemo = new IntersectionObserver(function (entrees) {
        if (entrees[0].isIntersecting) {
          observateurDemo.disconnect();
          setTimeout(lancerDemo, 600);
        }
      }, { threshold: 0.6 });
      observateurDemo.observe(slider);
    });
  }


  /* ---------------------------------------------------------------
     9. CARTE OPENSTREETMAP AU CLIC
     L'<iframe> n'existe pas dans le HTML : on la crée seulement quand
     le visiteur clique. Avant, aucune donnée ne part vers OpenStreetMap.
     --------------------------------------------------------------- */
  document.querySelectorAll('.map-consent').forEach(function (bloc) {
    var bouton = bloc.querySelector('.map-consent__btn');
    var adresse = bloc.getAttribute('data-map-src');
    if (!bouton || !adresse) return;

    bouton.addEventListener('click', function () {
      var iframe = document.createElement('iframe');
      iframe.src = adresse;
      iframe.title = "Carte de la zone d'intervention de Clean Elephant autour de Montmagny";
      iframe.loading = 'lazy';
      iframe.referrerPolicy = 'no-referrer';   // n'envoie pas l'adresse de notre page à OpenStreetMap
      bloc.textContent = '';                  // vide le bloc (texte + bouton)
      bloc.appendChild(iframe);
      bloc.classList.add('is-loaded');
    });
  });


  /* ---------------------------------------------------------------
     10. FILTRE « VOUS ÊTES » DU DEVIS
     Selon le profil choisi (particulier / professionnel), on cache les
     prestations qui ne le concernent pas. Une case cachée est aussi
     décochée, pour ne pas envoyer un choix que le visiteur ne voit plus.
     Sans JavaScript : toutes les prestations restent affichées.
     --------------------------------------------------------------- */
  document.querySelectorAll('[data-filtre-profil]').forEach(function (bloc) {
    var radios = bloc.querySelectorAll('input[name="profil"]');
    var items = bloc.querySelectorAll('[data-profil]');

    function appliquerFiltre() {
      var choisi = bloc.querySelector('input[name="profil"]:checked');
      var profil = choisi ? choisi.value : '';

      items.forEach(function (item) {
        var cible = item.getAttribute('data-profil');
        var visible = profil === '' || cible === 'tous' || cible === profil;
        item.hidden = !visible;
        if (!visible) {
          var caseACocher = item.querySelector('input');
          if (caseACocher) caseACocher.checked = false;
        }
      });
    }

    radios.forEach(function (radio) { radio.addEventListener('change', appliquerFiltre); });
    appliquerFiltre();
  });


  /* ---------------------------------------------------------------
     11. VIDÉO DE DÉMONSTRATION
     - preload="none" dans le HTML : rien n'est téléchargé tant que
       la vidéo n'approche pas de l'écran (page d'accueil plus rapide) ;
     - elle se lance quand elle est visible, se met en pause quand on
       s'en éloigne (pas de vidéo qui tourne pour rien) ;
     - si l'utilisateur réduit les animations, elle ne part pas seule :
       il clique sur le bouton pour la lancer ;
     - le bouton pause/lecture est obligatoire en accessibilité pour
       tout contenu qui bouge plus de 5 secondes (WCAG 2.2.2).
     --------------------------------------------------------------- */
  document.querySelectorAll('.video-demo__media').forEach(function (cadre) {
    var video = cadre.querySelector('video');
    var bouton = cadre.querySelector('[data-video-toggle]');
    if (!video || !bouton) return;

    var pauseVoulue = reduireAnimations;   // l'utilisateur a-t-il choisi la pause ?

    function majBouton() {
      var enLecture = !video.paused;
      cadre.classList.toggle('is-playing', enLecture);
      bouton.setAttribute('aria-label', enLecture ? 'Mettre la vidéo en pause' : 'Lire la vidéo');
    }
    function lire() {
      var promesse = video.play();
      if (promesse && promesse.catch) promesse.catch(function () {});   // lecture refusée par le navigateur : on reste sur l'image
    }

    bouton.addEventListener('click', function () {
      if (video.paused) { pauseVoulue = false; lire(); }
      else { pauseVoulue = true; video.pause(); }
    });
    video.addEventListener('play', majBouton);
    video.addEventListener('pause', majBouton);

    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entrees) {
        entrees.forEach(function (entree) {
          if (entree.isIntersecting && !pauseVoulue) lire();
          else if (!entree.isIntersecting) video.pause();
        });
      }, { threshold: 0.35 }).observe(video);
    } else if (!pauseVoulue) {
      lire();
    }
    majBouton();
  });
})();