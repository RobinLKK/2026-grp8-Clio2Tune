#include "header.h"

Tile** init_grid(int size) {
	Tile** grid = (Tile**)malloc(size * sizeof(Tile*));
	if (grid == NULL) return NULL;

	for (int i = 0; i < size; i++) {
		grid[i] = (Tile*)malloc(size * sizeof(Tile));
		if (grid[i] == NULL) return NULL;

		for (int j = 0; j < size; j++) {
			grid[i][j].ColorId = 0;
			grid[i][j].hasCar = false;
			grid[i][j].hasX = false;
		}
	}
	return grid;
}

bool safeligne(Tile** p, int ligne, int colonne, int size) {


	int l = ligne;
	int c = colonne;
	for (int j = 0; j < size; j++) {
		if (p[l][j].hasCar == true) {
			return false;
		}
	}
	for (int i = 0; i < size; i++) {
		if (p[i][c].hasCar == true) {
			return false;
		}
	}
	return true;
}
bool emptyregion(Tile** p, int ligne, int colonne, int size) {

	int targetcolor = &p[ligne][colonne].ColorId;
	for (int i = 0; i < size; i++) {
		for (int j = 0; j < size; j++) {
			if (i == ligne && j == colonne) {
				continue;
			}
			if (p[i][j].ColorId == targetcolor && p[i][j].hasCar) {
				return false;
			}
		}
	}
	return true;
}
bool safearound(Tile** p, int ligne, int colonne, int size) {
	int posX;
	int posY;
	for (int n = 0; n < 8; n++) {
		switch (n) {
		case 0:
			posX = ligne;
			posY = colonne - 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 1:
			posX = ligne - 1;
			posY = colonne - 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 2:
			posX = ligne - 1;
			posY = colonne;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 3:
			posX = ligne - 1;
			posY = colonne + 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 4:
			posX = ligne;
			posY = colonne + 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 5:
			posX = ligne + 1;
			posY = colonne + 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 6:
			posX = ligne + 1;
			posY = colonne;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		case 7:
			posX = ligne + 1;
			posY = colonne - 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				if (p[posX][posY].hasCar) return false;
			}
			break;
		}
	}
	return true;
}
void croixauto(Tile** p, int ligne, int colonne, int size) {
	int targetcolor = p[ligne][colonne].ColorId;
	for (int i = 0; i < size; i++) {
		for (int j = 0; j < size; j++) {
			if (p[i][j].ColorId == targetcolor) {
				p[i][j].hasX = true;
			}
			if (i == ligne || j == colonne) {
				p[i][j].hasX = true;
			}
		}
	}
	int posX;
	int posY;
	for (int n = 0; n < 8; n++) {
		switch (n) {
		case 0:
			posX = ligne;
			posY = colonne - 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 1:
			posX = ligne - 1;
			posY = colonne - 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 2:
			posX = ligne - 1;
			posY = colonne;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 3:
			posX = ligne - 1;
			posY = colonne + 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 4:
			posX = ligne;
			posY = colonne + 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 5:
			posX = ligne + 1;
			posY = colonne + 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 6:
			posX = ligne + 1;
			posY = colonne;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		case 7:
			posX = ligne + 1;
			posY = colonne - 1;
			if (posX >= 0 && posX < size && posY >= 0 && posY < size) {
				p[posX][posY].hasX = true;
			}
			break;
		}
	}
}

bool estPlacable(Tile** p, int ligne, int colonne, int size) {
	if (safeligne(p, ligne, colonne, size) &&
		emptyregion(p, ligne, colonne, size) &&
		safearound(p, ligne, colonne, size)) {
		return true;
	}
	else {
		return false;
	}
}
void placerVoiture(Tile** p, int ligne, int colonne, int size) {
	if (estPlacable(p, ligne, colonne, size)) {
		p[ligne][colonne].hasCar == true;
	}
	else
	{
		printf("Impossible de placer une voiture ici ! \n");
	}
}
void placerCroix(Tile** p, int ligne, int colonne, int size) {
	if (p[ligne][colonne].hasCar) {
		p[ligne][colonne].hasCar = false;
		p[ligne][colonne].hasX = true;
	}
	else if (p[ligne][colonne].hasX == true)
	{
		p[ligne][colonne].hasX == false;
	}
	else
	{
		p[ligne][colonne].hasX == true;
	}
}

void libererPlateau(Tile** p, int size) {
	for (int i = 0; i < size; i++) {
		free(p[i]); 
	}
	free(p); 
}
void afficherPlateau(Tile** p, int size) {
	printf("\n   ");
	for (int p = 0; p < size; p++) {
		printf("%d ", p);
	}
	printf("\n");
	for (int i = 0; i < size; i++) {
		printf("%d /", i);
		for (int j = 0; j < size; j++) {
			if (p[i][j].hasCar) {
				printf("V ");
			}
			if (p[i][j].hasX)
			{
				printf("x ");
			}
			else
			{
				printf("- ");
			}
		}
		printf("\n");
	}
}
bool VerifierVictoire(Tile** p, int size) {
	int Cars = 0;
	for (int i = 0; i < size; i++) {
		for (int j = 0; j < size; j++) {
			if (p[i][j].hasCar) {
				Cars++;
			}
		}
	}
	if (Cars == size) {
		return true;
	}
	else
	{
		return false;
	}
}