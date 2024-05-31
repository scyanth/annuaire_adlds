<!doctype html>
<html lang="fr">
  <head>
    <title>Annuaire company - Recherche de personnes</title>
    <meta charset="utf-8">
    <style>
      body {
        font-family: Arial, Tahoma, Verdana;
      }   
      .table_invis {
        border-collapse: collapse;
        border-spacing: 0px;
      }
      .table_vis {
        border-collapse: separate;
        border-spacing: 1px;
        table-layout: auto;
        width: auto;
      }
      .table_invis > tr > :first-child, .table_invis th, .table_invis td {
        border: none;
        background-color: transparent;
        padding : 0px;
      }
      .table_vis table tr > :first-child {
        background-color: #D3D3D3;

      }
      .table_vis td, .table_vis th {
        border: 1px solid;
        white-space: nowrap;
        padding: 10px;
      }
      .center {
          text-align: center;
          margin-left: auto;
          margin-right: auto;
      }
    </style>
  </head>
  <body>

<?php

// -------------------------------------------------------------------------------------------------------------
// initialisation
// -------------------------------------------------------------------------------------------------------------

require_once("init.php");

// -------------------------------------------------------------------------------------------------------------
// controle du formulaire
// -------------------------------------------------------------------------------------------------------------

if (isset($_POST['demandeRecherche'])){
    if (($_POST["mot_cle"] != "") && ($_POST["mot_cle"] != " ")){
        // escapade
        $mot_cle = htmlspecialchars($_POST["mot_cle"]);
        if ($_POST["search_type"] == "contient"){
          // interdiction de motifs plus petits que 3 caractères pour les recherches "contient"
          if (strlen($mot_cle) >= 3){
            // bind l'AD LDS
            $entrees = bind_ad_lds($mot_cle,"contient");
            if (!($entrees)){
              $message = "Erreur LDAP !";
            }
          }else{
            $message = "Erreur : le mot-clé est trop court !";
          }
        }else{
          // interdiction du wildcard pour les recherches "egal"
          if (strpos($mot_cle,"*") === false){
            $entrees = bind_ad_lds($mot_cle,"egal");
            if (!($entrees)){
              $message = "Erreur LDAP !";
            }
          }else{
            $message = 'Erreur : caractère non autorisé.';
          }

        }
    }else{
        $message = "Erreur : veuillez remplir le champ !";
    }
}

// -------------------------------------------------------------------------------------------------------------
// affichage
// -------------------------------------------------------------------------------------------------------------

print '<table class="center">';
print "<tr><td><h2> Annuaire company - Recherche de personnes </h2></td></tr>";
print '<tr><td><img class="center" src="images/company_entites_logos.PNG"></img></td></tr>';
print '<tr><td><br/><br/><br/><br/></td></tr>';

print '<tr><td><form action="annuaire_company.php" method="post"> <table class="center table_invis"> <thead> <tr> <th>  </th> <th>Mot-clé</th> </tr> </thead> <tbody> <tr>';
print '<td><select name="search_type"><option value="egal">EGAL</option><option value="contient">CONTIENT</option></select></td><td><input type="text" name="mot_cle"></td>';
print '</tr> <tr> <td colspan="2"> <button class="center" type="submit" name="demandeRecherche">Rechercher</button> </td> </tr> </tbody> </table> </form></td></tr></table>';

if (isset($entrees)){

  if ($entrees[0]['dn'] == ""){
    print '<p class="center">Aucun résultat.</p>';
    exit();
  }

  if (sizeof($entrees) > 1){
    if ($entrees[1]['dn'] != ""){
      print '<p class="center">Plusieurs personnes ont été trouvées :</p>';
    }else{
      print '<p class="center">Une personne a été trouvée :</p>';
    }
  }else{
    print '<p class="center">Une personne a été trouvée :</p>';
  }

  print '<table class="center table_vis">';

  foreach ($entrees as $individu){

    $dn = $individu['dn'];

    // iu
    if (strpos($dn,"OU=A,OU=S,OU=U,OU=iu,OU=C") !== false){
        print '<tr><td><table class="center table_vis">';
        print '<tr><td>Entité </td><td><img src="images/company_iu_logo.png" alt="iu"></img></td></tr>';
        print '<tr><td>Identifiant </td><td>'.$individu['cn'][0].'</td></tr>';
        print '<tr><td>Nom complet </td><td>'.$individu['displayname'][0].'</td></tr>';
        print '<tr><td>Prénom </td><td>'.$individu['givenname'][0].'</td></tr>';
        print '<tr><td>Nom de famille </td><td>'.$individu['sn'][0].'</td></tr>';
        print '<tr><td>Adresse mail </td><td>'.$individu['mail'][0].'</td></tr>';
        print '<tr><td>Identifiant up </td><td>'.$individu['iuloginup'][0].'</td></tr>';
        print '<tr><td>Catégorie </td><td>'.$individu['iucatusager'][0].'</td></tr>';
        print '<tr><td>Affectation principale </td><td>'.$individu['iuaffectationprinc'][0].'</td></tr>';
        print '</table></td></tr>';
    // in
    }elseif (strpos($dn,"OU=S,OU=U,OU=i,OU=C") !== false){
        print '<tr><td><table class="center table_vis">';
        print '<tr><td>Entité </td><td><img src="images/company_in_logo.png" alt="in"></img></td></tr>';
        print '<tr><td>Identifiant </td><td>'.$individu['cn'][0].'</td></tr>';
        print '<tr><td>Prénom </td><td>'.$individu['givenname'][0].'</td></tr>';
        print '<tr><td>Nom de famille </td><td>'.$individu['sn'][0].'</td></tr>';
        print '<tr><td>Adresse mail </td><td>'.$individu['mail'][0].'</td></tr>';
        print '<tr><td>Catégorie </td><td>'.$individu['employeetype'][0].'</td></tr>';
        print '</table></td></tr>';
    // is
    }elseif (strpos($dn,"OU=S,OU=U,OU=is,OU=C") !== false){
        print '<tr><td><table class="center table_vis">';
        print '<tr><td>Entité </td><td><img src="images/company_is_logo.png" alt="is"></img></td></tr>';
        print '<tr><td>Identifiant </td><td>'.$individu['cn'][0].'</td></tr>';
        print '<tr><td>Adresse mail </td><td>'.$individu['mail'][0].'</td></tr>';
        // contrôle du domaine mail
        $mail_parts = explode("@",$individu['mail'][0]);
        if ($mail_parts[1] == "is.fr"){
            print '<tr><td>Catégorie </td><td>Personnel</td></tr>';
        }else{
            if ($individu['title'][0]){
                print '<tr><td>Catégorie </td><td>'.$individu['title'][0].'</td></tr>';
            }else{
                print '<tr><td>Catégorie </td><td>Inconnu</td></tr>';
            }
        }
        print '</table></td></tr>';
    // up
    }elseif (strpos($dn,"OU=S,OU=U,OU=f,OU=C") !== false){
        print '<tr><td><table class="center table_vis">';
        print '<tr><td>Entité </td><td><img src="images/company_up_logo.png" alt="up"></img></td></tr>';
        print '<tr><td>Identifiant </td><td>'.$individu['cn'][0].'</td></tr>';
        print '<tr><td>Prénom </td><td>'.$individu['givenname'][0].'</td></tr>';
        print '<tr><td>Nom de famille </td><td>'.$individu['sn'][0].'</td></tr>';
        print '<tr><td>Adresse mail </td><td>'.$individu['mail'][0].'</td></tr>';
        print '<tr><td>Catégorie </td><td>';
        // il peut y avoir plusieurs catégories
        foreach ($individu["edupersonaffiliation"] as $aff){
          // on ignore les valeurs numériques et la valeur "member"
          if ((!(is_numeric($aff))) && ($aff != "Member")){
            print $aff.'<br/>';
          }
        }
        print '</td></tr>';
        print '</table></td></tr>';
    }

  }
  print '</table>';
}

if (isset($message)){
    print '<p class="center">'.$message.'</p>';
}


// -------------------------------------------------------------------------------------------------------------
// fonction de bind de l'AD LDS
// -------------------------------------------------------------------------------------------------------------

function bind_ad_lds($mot_cle,$search_type){
    // definition du filtre selon le type de recherche
    if ($search_type == "egal"){
      $filtre = "(|(name=$mot_cle)(cn=$mot_cle)(sn=$mot_cle)(displayName=$mot_cle)(givenName=$mot_cle)(uid=$mot_cle)(initials=$mot_cle)(gecos=$mot_cle)(ou=$mot_cle)(dc=$mot_cle)(o=$mot_cle)(group=$mot_cle)(dmdName=$mot_cle)(sAMAccountName=$mot_cle)(description=$mot_cle)(labeledURI=$mot_cle))";
    }elseif ($search_type == "contient"){
      $filtre = "(|(name=*$mot_cle*)(cn=*$mot_cle*)(sn=*$mot_cle*)(displayName=*$mot_cle*)(givenName=*$mot_cle*)(uid=*$mot_cle*)(initials=*$mot_cle*)(gecos=*$mot_cle*)(ou=*$mot_cle*)(dc=*$mot_cle*)(o=*$mot_cle*)(group=*$mot_cle*)(dmdName=*$mot_cle*)(sAMAccountName=*$mot_cle*)(description=*$mot_cle*)(labeledURI=*$mot_cle*))";
    }
    // connexion au serveur AD LDS
    try {
        $ADHost="ldap://".$_ENV["AD_LDS_HOST"];
        $adconn = ldap_connect($ADHost, 636);
        // parametres LDAP
        ldap_set_option($adconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($adconn, LDAP_OPT_REFERRALS, 0);
        // bind avec le compte de service
        $login_svc = $_ENV["AD_LDS_CN"];
        $mdp_svc = $_ENV["AD_LDS_PASSWORD"];
        $admin_bind = ldap_bind($adconn, $login_svc, $mdp_svc);
        if ($admin_bind) {
            // recherche du mot-clé
            $index = ldap_search($adconn,"DC=company,DC=LOCAL",$filtre);
            $entrees = ldap_get_entries($adconn,$index);
            // fermeture de la connexion
            ldap_close($adconn);
            return $entrees;
        }
    }catch (error $e) {
        return false;
    }
  }


  ?>

   </body>
</html>