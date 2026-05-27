#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <time.h>
#include <string.h>

typedef struct {
    int ColorId;
    bool hasCar;
} Tile;


void compterSolutions(Tile** g, int r, int size, int* count);
bool estPlacable(Tile** g, int r, int c, int size);


Tile** init_grid(int size) {
    Tile** g = (Tile**)malloc(size * sizeof(Tile*));

    if (g == NULL) return NULL;

    for (int i = 0; i < size; i++) {
        g[i] = (Tile*)malloc(size * sizeof(Tile));

        if (g[i] == NULL) {
            for (int k = 0; k < i; k++) {
                free(g[k]);
            }
            free(g);
            return NULL;
        }

        for (int j = 0; j < size; j++) {
            g[i][j].ColorId = -1;
            g[i][j].hasCar = false;
        }
    }
    return g;
}

void free_grid(Tile** g, int size) {
    if (g == NULL) return;
    for (int i = 0; i < size; i++) free(g[i]);
    free(g);
}

// --- LOGIQUE DE GÉNÉRATION OPTIMISÉE ---

bool peutPlacerGen(Tile** g, int r, int c, int size) {
    for (int i = 0; i < r; i++) {
        for (int j = 0; j < size; j++) {
            if (g[i][j].hasCar) {
                // Utilisation de abs() sans malloc
                int diffR = i - r; if (diffR < 0) diffR = -diffR;
                int diffC = j - c; if (diffC < 0) diffC = -diffC;
                if (j == c || (diffR <= 1 && diffC <= 1)) return false;
            }
        }
    }
    return true;
}

bool placerReinesAlea(Tile** g, int r, int size) {
    if (r == size) return true;

    int cols[20];
    for (int i = 0; i < size; i++) cols[i] = i;

    
    for (int i = size - 1; i > 0; i--) {
        int j = rand() % (i + 1);
        int t = cols[i]; cols[i] = cols[j]; cols[j] = t;
    }

    for (int i = 0; i < size; i++) {
        int c = cols[i];
        if (peutPlacerGen(g, r, c, size)) {
            g[r][c].hasCar = true;
            g[r][c].ColorId = r;
            if (placerReinesAlea(g, r + 1, size)) return true;
            g[r][c].hasCar = false;
            g[r][c].ColorId = -1;
        }
    }
    return false;
}

void propagerCouleurs(Tile** g, int size) {
    int reste = (size * size) - size;
    static const int dr[] = { -1, 1, 0, 0 };
    static const int dc[] = { 0, 0, -1, 1 };

    int maxIter = size * size * 30;
    int iter = 0;
    while (reste > 0 && iter < maxIter) {
        iter++;
        int r = rand() % size;
        int c = rand() % size;
        if (g[r][c].ColorId != -1) {
            int d = rand() % 4;
            int nr = r + dr[d];
            int nc = c + dc[d];
            if (nr >= 0 && nr < size && nc >= 0 && nc < size && g[nr][nc].ColorId == -1) {
                g[nr][nc].ColorId = g[r][c].ColorId;
                reste--;
            }
        }
    }
}

// --- SOLVEUR ULTRA-RAPIDE ---

bool estPlacable(Tile** g, int r, int c, int size) {
    // Colonne et Ligne
    for (int i = 0; i < size; i++) {
        if (i != c && g[r][i].hasCar) return false;
        if (i != r && g[i][c].hasCar) return false;
    }
    // Couleur
    int color = g[r][c].ColorId;
    for (int i = 0; i < size; i++) {
        for (int j = 0; j < size; j++) {
            if ((i != r || j != c) && g[i][j].ColorId == color && g[i][j].hasCar) return false;
        }
    }
    // Adjacence (les 8 cases)
    for (int i = r - 1; i <= r + 1; i++) {
        for (int j = c - 1; j <= c + 1; j++) {
            if (i >= 0 && i < size && j >= 0 && j < size) {
                if ((i != r || j != c) && g[i][j].hasCar) return false;
            }
        }
    }
    return true;
}

void compterSolutions(Tile** g, int r, int size, int* count) {
    if (*count > 1) return;
    if (r == size) { (*count)++; return; }
    for (int c = 0; c < size; c++) {
        if (estPlacable(g, r, c, size)) {
            g[r][c].hasCar = true;
            compterSolutions(g, r + 1, size, count);
            g[r][c].hasCar = false;
        }
    }
}

// --- MAIN ---

int main(int argc, char** argv) {
    int size = (argc > 1) ? atoi(argv[1]) : 8;
    if (size < 4 || size > 15) size = 8;
    srand((unsigned int)time(NULL));

    Tile** g = init_grid(size);
    int solCount = 0;
    int tentatives = 0;

    while (tentatives < 2000) {
        tentatives++;

        for (int i = 0; i < size; i++) {
            for (int j = 0; j < size; j++) {
                g[i][j].ColorId = -1;
                g[i][j].hasCar = false;
            }
        }

        if (!placerReinesAlea(g, 0, size)) continue;
        propagerCouleurs(g, size);

        for (int i = 0; i < size; i++)
            for (int j = 0; j < size; j++) g[i][j].hasCar = false;

        solCount = 0;
        compterSolutions(g, 0, size, &solCount);

        if (solCount == 1) break;
    }

    printf("{\"size\":%d,\"map_data\":\"", size);
    for (int i = 0; i < size; i++) {
        for (int j = 0; j < size; j++) {
            int id = g[i][j].ColorId;
            if (id < 10) printf("%c", id + '0');
            else printf("%c", id - 10 + 'A');
        }
    }
    printf("\"}");

    free_grid(g, size);
    return 0;
}