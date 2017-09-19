<?php
// function compteur
function decollage()
{
        
        static $compteur = 5;
        echo $compteur;
        $compteur--;
        if($compteur >= 0)
        {
                decollage();
        }
}
decollage(); 

// function factorielle
function factorielle($nbre)
{
        //Si $nbre = 0 on retourne 1 car soit 1! = 1, soit on est arrivés à la fin du calcul
        if($nbre === 0)
        {
                return 1;
        }
        else //Sinon on retourne le nombre multiplié par le reste de sa factorielle (avec $nbre décrémenté)
        {
                return $nbre*factorielle($nbre-1);
        }
}
//On affiche la factorielle de 6
echo factorielle(6);
?>