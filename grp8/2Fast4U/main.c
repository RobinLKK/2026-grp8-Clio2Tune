#include "header.h"



int main() {
    srand(time(NULL));
    int size;

    printf("=========================================\n");
    printf("   2FAST4U      \n");
    printf("=========================================\n");

    printf("Entrez la taille du plateau (ex: 8) : ");
    if (scanf("%d", &size) != 1) return 1;

    if (size < 4 || size > 12) {
        printf("Taille conseillée entre 4 et 12.\n");
        return 1;
    }

    // 1. Initialisation et Génération
    Tile** plateau = init_grid(size);
    if (plateau == NULL) return 1;

    printf("\nGeneration d'un niveau soluble en cours...\n");
    genererNiveauGagnable(plateau, size);

    int ligne, col, choix;
    bool enJeu = true;

    // 2. Boucle de Jeu
    while (enJeu) {
        afficherJeuCouleur(plateau, size);

        printf("\nActions: 1. Voiture | 2. Croix | 0. Quitter\n");
        printf("Commande (Action Ligne Colonne) : ");

        if (scanf("%d %d %d", &choix, &ligne, &col) != 3) {
            printf("Entree invalide. Format: Action Ligne Colonne\n");
            while (getchar() != '\n'); // Purger le buffer
            continue;
        }

        if (choix == 0) break;

        // Vérification des limites de coordonnées
        if (ligne >= 0 && ligne < size && col >= 0 && col < size) {

            if (choix == 1) {
                // Si la case a déjà une voiture, on l'enlève (Toggle)
                if (plateau[ligne][col].hasCar) {
                    plateau[ligne][col].hasCar = false;
                }
                else {
                    // Sinon on vérifie si on peut la poser
                    if (safeligne(plateau, ligne, col, size) &&
                        emptyregion(plateau, ligne, col, size) &&
                        safearound(plateau, ligne, col, size)) {

                        plateau[ligne][col].hasCar = true;
                        plateau[ligne][col].hasX = false;
                        croixauto(plateau, ligne, col, size);
                    }
                    else {
                        printf("\033[31m\n!! COUP INVALIDE (Regles non respectees) !!\033[0m\n");
                    }
                }
            }
            else if (choix == 2) {
                placerCroix(plateau, ligne, col, size);
            }
        }
        else {
            printf("Coordonnees hors plateau !\n");
        }

        // 3. Vérification Victoire
        if (VerifierVictoire(plateau, size)) {
            afficherJeuCouleur(plateau, size);
            printf("\n=========================================\n");
            printf("   FELICITATIONS ! Vous avez gagne !     \n");
            printf("=========================================\n");
            enJeu = false;
        }
    }

    // 4. Nettoyage
    libererPlateau(plateau, size);
    printf("\nPartie terminee. A bientot !\n");

    return 0;
}