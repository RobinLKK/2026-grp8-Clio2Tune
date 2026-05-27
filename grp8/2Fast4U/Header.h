#pragma once
#define _CRT_SECURE_NO_WARNINGS
#include <stdlib.h>
#include <stdbool.h>
#include <stdio.h>
#define MAX_SIZE 12
#include <time.h>





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

//Affiche le board
//void afficherPlateau(Tile** p, int size);

//Vérifie si la partie est gagner
bool VerifierVictoire(Tile** p, int size);

//attibue une zone de couleur a chaque case
//void genererZones(Tile** p, int size, int nbZones);

//Vérification pour placer une reine lors de la génération
bool peutPlacerPourGen(Tile** p, int r, int c, int size);

//donne les coordonées pour placer unne reine aléatoirement contient la fonction de vérification
bool placerReinesAlea(Tile** p, int r, int size);

//génére les zones de couleur ainsi qu'un plateau gagnable
void genererNiveauGagnable(Tile** p, int size);

//fonction d'affichage coloré (non faites à la main et probablement temporaire) 
void afficherJeuCouleur(Tile** p, int size);
//moteur du solveur
bool moteur(Tile** p, int r, int size);

