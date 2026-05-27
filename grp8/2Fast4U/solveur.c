#include "Header.h"
int main(int argc, char** argv) {
    if (argc < 3) { printf("{\"error\":\"Arguments manquants\"}"); return 0; }
    srand((unsigned int)time(NULL));
    int size = atoi(argv[1]);
    char* map = argv[2];
    char* user = (argc > 3) ? argv[3] : NULL; // Le 3ème argument est optionnel au cas où

    Tile** p = init_grid(size);
    for (int i = 0; i < size; i++) {
        for (int j = 0; j < size; j++) {
            char c = map[i * size + j];
            if (c >= '0' && c <= '9') p[i][j].ColorId = c - '0';
            else p[i][j].ColorId = 10 + (c - 'A');
        }
    }

    if (moteur(p, 0, size)) {
        int candR[144], candC[144], count = 0;
        for (int i = 0; i < size; i++) {
            for (int j = 0; j < size; j++) {
                if (p[i][j].hasCar) {
                    // On n'ajoute en candidat que si le joueur n'a pas DEJA mis une voiture là
                    if (!user || user[i * size + j] == '0') {
                        candR[count] = i; candC[count] = j; count++;
                    }
                }
            }
        }
        if (count > 0) {
            int sel = rand() % count;
            printf("{\"r\":%d,\"c\":%d}", candR[sel], candC[sel]);
        }
        else {
            printf("{\"error\":\"Deja placees\"}");
        }
    }
    else { printf("{\"error\":\"Pas de solution\"}"); }
    return 0;
}