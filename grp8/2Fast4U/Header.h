#pragma once

#include <stdlib.h>
#include <stdbool.h>
#include <stdio.h>
#define MAX_SIZE 12






typedef struct {
	int ColorId; //Entier indiquant la zone Couleur 
	bool hasCar; //booléen si true il y a une voiture si false il n'y en a pas
	bool hasX; //booléen si true il y a une croix si false il n'y en a pas
}Tile;


//allocation dynamique et création de la grille
Tile** init_grid(int size);

//vérifie qu'il n'y a pas de voiture sur la ligne et sur la colonne
bool safeligne(Tile** p, int ligne, int colonne, int size);

//vérifie qu'il n'y a pas de voiture sur la meme couleur
bool emptyregion(Tile** p, int ligne, int colonne, int size);

//verifie qu'il n'y a pas de coiture aux alentours (8 cases autour)
bool safearound(Tile** p, int ligne, int colonne, int size);

//Place les croix de manière automatique autour de la voiture et en croix autour d'elle
void croixauto(Tile** p, int ligne, int colonne, int size);

//Vérfie que l'on peut placer un objet en fonction de la ligne et colonne
bool estPlacable(Tile** p, int ligne, int colonne, int size);

//Quand le jouer Clique sur une case
//void CliqueCase(Tile** p, int ligne, int colonne, int size);

//Place une voiture
void placerVoiture(Tile** p, int ligne, int colonne, int size);

//Place une croix
void placerCroix(Tile** p, int ligne, int colonne, int size);

//supprime l'allocation de la grille
void libererPlateau(Tile** p, int size);