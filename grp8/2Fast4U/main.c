#include "header.h"
int main() {
    int size = 5; 
    Tile** plateau = init_grid(size);

    
    for (int i = 0; i < size; i++) {
        for (int j = 0; j < size; j++) {
            plateau[i][j].ColorId = i;
        }
    }

    int ligne, col, choix;
    bool enJeu = true;

    printf("--- JEU DE LA REINE (VOITURE) ---\n");

    while (enJeu) {
        afficherPlateau(plateau, size);
        printf("\nActions: 1. Voiture | 2. Croix | 0. Quitter\n");
        printf("Entrez Action Ligne Colonne (ex: 1 0 2) : ");

        if (scanf("%d %d %d", &choix, &ligne, &col) != 3) break;

        if (choix == 0) break;

        if (ligne >= 0 && ligne < size && col >= 0 && col < size) {
            if (choix == 1) {

                if (safeligne(plateau, ligne, col, size) &&
                    emptyregion(plateau, ligne, col, size) &&
                    safearound(plateau, ligne, col, size)) {

                    plateau[ligne][col].hasCar = !plateau[ligne][col].hasCar;
                    if (plateau[ligne][col].hasCar) {
                        plateau[ligne][col].hasX = false;
                        croixauto(plateau, ligne, col, size);
                    }
                }
                else {
                    printf("\a!! Coup invalide selon les regles !!\n");
                }
            }
            else if (choix == 2) {
                placerCroix(plateau, ligne, col, size);
            }
        }

        if (VerifierVictoire(plateau, size)) {
            afficherPlateau(plateau, size);
            printf("\nBRAVO ! Vous avez gagne !\n");
            enJeu = false;
        }
    }

    libererPlateau(plateau, size);
    return 0;
}