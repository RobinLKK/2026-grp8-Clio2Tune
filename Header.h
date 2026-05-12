#pragma once

#include <stdlib.h>
#include <stdbool.h>
#include <stdio.h>

typedef struct {
	int ColorId; //Entier indiquant la zone Couleur 
	bool hasPiece; //booléen si true il y a une pièce si false il n'y en a pas
	bool hasX; //booléen si true il y a une croix si false il n'y en a pas
}Plateau;
//Vérfie que l'on peut placer un objet en fonction de la ligne et colonne
bool estPlacable(int ligne, int colonne);

//


