﻿<?php
	require_once("connexion.php");
	$oscom=new oscom;
	$annee=$_GET['millesime'];
	$lien_meta=$oscom->catalogue_geoide($_GET['millesime']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE></TITLE>
	<META NAME="GENERATOR" CONTENT="LibreOffice 4.1.5.3 (Windows)">
	<META NAME="CREATED" CONTENT="20160415;111256697000000">
	<META NAME="CHANGED" CONTENT="20160415;121337779000000">
	<STYLE TYPE="text/css">
	<!--
		@page { margin: 2cm }
		P { margin-bottom: 0.21cm }
		A:link { so-language: zxx }
	-->
	</STYLE>
</HEAD>
<BODY LANG="fr-FR" DIR="LTR">
<P STYLE="margin-bottom: 0cm"><B>OSCOM mill&eacute;sime <?php echo $annee ?></B></P>
<P STYLE="margin-bottom: 0cm">L&rsquo;ensemble des M&eacute;tadonn&eacute;es
est disponible sur le site national&nbsp;&laquo;&nbsp;<A HREF="http://catalogue.geo-ide.developpement-durable.gouv.fr/" TARGET="_blank">Catalogue
Interminist&eacute;riel de Donn&eacute;es G&eacute;ographiques</A>&nbsp;&raquo;</P>
<P STYLE="margin-bottom: 0cm">Les m&eacute;tadonn&eacute;es de
l&rsquo;ann&eacute;e <?php echo $annee ?> sont consultables <?php echo '<a href="'.$lien_meta.'" target="_blank">en cliquant ici</a>' ?></P>
<P STYLE="margin-bottom: 0cm">L&rsquo;outil OSCOM permet d&rsquo;estimer
l'occupation du sol selon une approche bas&eacute;e sur un
traitement de couches graphiques issues notamment de la BD-TOPO&reg;
et de la BD-FORET&reg; de l'IGN, du Registre Parcellaire Graphique
(RPG) de l'ASP et de la base Majic de la DGFiP.</P>
<P STYLE="margin-bottom: 0cm">Cette approche permet de g&eacute;n&eacute;rer
facilement diff&eacute;rents mill&eacute;simes de la couche
d'occupation du sol de mani&egrave;re &agrave; estimer les &eacute;volutions
surfaciques d&rsquo;une ann&eacute;e &agrave; l'autre.</P>
<P STYLE="margin-bottom: 0cm">Cette compilation de fichiers
informatiques de mill&eacute;sime et de pr&eacute;cision h&eacute;t&eacute;rog&egrave;nes
implique de prendre des pr&eacute;cautions quant &agrave; l'usage des
r&eacute;sultats obtenus.</P>
<P STYLE="margin-bottom: 0cm">L&rsquo;outil fonctionne sous
PostGreSQL (version 9.3 et post&eacute;rieure) qui est un SGBDR
(Syst&egrave;me de Gestion de Bases de Donn&eacute;es Relationnelles)
libre permettant de traiter les objets g&eacute;o-r&eacute;f&eacute;renc&eacute;s
(unions, intersections, &hellip;) gr&acirc;ce &agrave; un module
additionnel nomm&eacute; PostGIS (version 2.1 et post&eacute;rieure).</P>
<P STYLE="margin-bottom: 0cm">Chaque service peut construire son
OSCOM &agrave; partir du <A HREF="http://valor.national.agri/R23-01-Haute-Normandie-Occupation?id_rubrique=187" TARGET="_blank">kit
diffus&eacute; sur le site VALOR</A>. Un <A HREF="http://valor.national.agri/IMG/pdf/20160325-OSCOM_20160203_cle83797f.pdf" TARGET="_blank">document
pdf est aussi disponible en t&eacute;l&eacute;chargement</A></P>
</BODY>
</HTML>