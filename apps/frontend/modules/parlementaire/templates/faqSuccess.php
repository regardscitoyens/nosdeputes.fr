<h1>Questions fréquemment posées</h1>
<div class="table_des_matieres">
  <ul>
    <li><a href="#post_1">Origine et nature des données publiées</a></li>
    <ul>
      <li><a href="#post_2">D'où viennent les données publiées sur le site ?</a></li>
      <li><a href="#post_3">Avez vous le droit de publier ces données ?</a></li>
      <li><a href="#post_4">Dans les graphes des députés et la synthèse, quelle est la différence entre les "présences détectées", les "présences enregistrées" et la présence "médiane des députés" ?</a></li>
      <li><a href="#post_5">Mais alors, pour les données en hémicycle, comment faites vous la différence entre une participation et une présence puisque vous détectez la présence grâce aux interventions ?</a></li>
      <li><a href="#post_6">Quelle est la différence entre une "intervention courte" et une "intervention longue" ?</a></li>
      <li><a href="#post_8">Pourquoi ne donnez-vous pas également accès aux votes des députés ?</a></li>
      <li><a href="#post_9">Pourquoi ne prenez-vous pas en compte le travail des députés dans leur circonscription ?</a></li>
      <li><a href="#post_10">Sur la page synthèse, pourquoi ne proposez-vous pas un classement des députés ?</a></li>
      <li><a href="#post_31">Que représentent les « vacances parlementaires » indiquées sur les graphiques ?</a></li>
      <li><a href="#post_27">Que représentent les « semaines d'activité » indiquées pour chaque député ?</a></li>
      <li><a href="#post_28">Prenez-vous en compte tous les travaux effectués à l'Assemblée nationale ?</a></li>
      <li><a href="#post_32">Que représentent les nombres indiqués dans la barre « Activité » sur la page de chaque député ?</a></li>
    </ul>
    <li><a href="#post_11">Les responsables du site</a></li>
    <ul>
      <li><a href="#post_12">Comment peut-on vous contacter ?</a></li>
      <li><a href="#post_13">Etes vous affiliés à un parti ou à une organisation politique ?</a></li>
      <li><a href="#post_14">Avez-vous été payés pour réaliser ce site ?</a></li>
      <li><a href="#post_15">Quelles sont vos sources d'inspiration ?</a></li>
    </ul>
    <li><a href="#post_16">Utilisation du site</a></li>
    <ul>
      <li><a href="#post_17">Pourquoi dois-je créer un compte pour pouvoir déposer un commentaire ?</a></li>
      <li><a href="#post_18">Comment puis-je relayer mon activité du site sur mes réseaux sociaux ?</a></li>
      <li><a href="#post_19">Comment puis-je vous aider ?</a></li>
      <li><a href="#post_21">Proposez-vous un suivi par alertes e-mails ?</a></li>
      <li><a href="#post_33">Quelles sont les rêgles d'encadrement des commentaires ?</a></li>
    </ul>
    <li><a href="#post_20">Fonctionnalités techniques</a></li>
    <ul>
      <li><a href="#post_26">Est ce que ce site est un logiciel libre ?</a></li>
      <li><a href="#post_22">Proposez-vous une API ?</a></li>
      <li><a href="#post_23">Quelles sont les technologies employées pour la création ce site ?</a></li>
    </ul>
  </ul>
</div>
<div id="faq">
  <h2 id="post_1">Origine et nature des données publiées</h2>

  <h3 id="post_2">D'où viennent les données publiées sur le site ?</h3>
  <p>Toutes les données relatives à l'activité parlementaire présentées sur ce site sont issues du site de l'<a rel="nofollow" href="http://www.assemblee-nationale.fr/">Assemblée nationale</a> et du Journal Officiel. Il s'agit donc d'informations intégralement publiques. Ces informations sont mises-à-jour toutes les 8 heures à partir des sites officiels. Les compte-rendus des débats peuvent parfois mettre plusieurs heures voire plusieurs jours avant d'être publiés par les services de l'Assemblée, les données des dernières semaines sont donc amenées à évoluer régulièrement et ne sauraient refléter la présence en temps réel des députés à l'Assemblée.</p>
  <p>Les cartes utilisées sont librement adaptées de celles remises à disposition par Wikipedia, Toxicode et Jérôme Cukier. Vous pouvez les télécharger sur <a href="http://www.nosdonnees.fr/package?q=circonscriptions+carte">NosDonnées.fr</a>.</p>

  <h3 id="post_3">Avez vous le droit de publier ces données ?</h3>
  <p>Oui, cela est précisé sur le site de l'<a rel="nofollow" href="http://www.assemblee-nationale.fr/faq.asp">Assemblée nationale</a> "<i>Les débats et les documents parlementaires [...] peuvent donc être reproduits librement [sauf] à des fins commerciales ou publicitaires</i>".</p>

  <h3 id="post_4">Dans les graphes des députés et la synthèse, quelle est la différence entre les "présences détectées", les "présences enregistrées" et la présence "médiane des députés" ?</h3>
  <p>Si les présences en commission sont enregistrées et diffusées publiquement à tous, les présences en hémicycle ne le sont pas. Pour détecter la présence d'un député en séance d'hémicycle, nous détectons sa participation orale au sein des compte-rendus de séance diffusés. Il est cependant rare que tous les députés présents parlent lors d'une séance et nous ne pouvons donc pas comptabiliser de manière exhaustive les présences des députés en hémicycle. C'est également pour cela que le graphe d'activité globale d'un député indique des présences détectées puisqu'il combine les données des commissions et de l'hémicycle.</p><p>Dans les graphes d'activité, la présence médiane affichée en liseré gris vise à apporter un élément de comparaison entre les différents graphes. Elle indique pour chaque semaine le nombre de réunions ou séances auxquelles a été détecté présent le député médian, c'est-à-dire qu'au moins la moitié des députés a été détecté présent autant de fois cette semaine là.</p>

  <h3 id="post_5">Mais alors, pour les données en hémicycle, comment faites vous la différence entre une participation et une présence puisque vous détectez la présence grâce aux interventions ?</h3>
  <p>Un député est compté comme participant en hémicycle si au moins une de ses interventions pendant la séance étudiée comporte plus de 20 mots. Vous pourrez en effet constater comme nous que les très courtes interventions ne peuvent pas aborder le fond du débat.</p>

  <h3 id="post_6">Quelle est la différence entre une "intervention courte" et une "intervention longue" ?</h3>
  <p>Une intervention courte est une intervention de moins de 20 mots. Comme expliqué précédemment, il s'agit d'interventions qui n'abordent pas le fond du débat, qu'il s'agisse pour le président de la séance d'annonces de prises de parole ou de simples interjections.</p>

  <h3 id="post_8">Pourquoi ne donnez-vous pas également accès aux votes des députés ?</h3>
  <p>Cela va venir. Nous n'avons pas encore eu le temps de nous pencher sur la question et leur intégration demande un travail approfondi car seules les données de certains votes sont publiques. En effet une grande majorité des votes se font à main levée et lorsqu'un scrutin public a lieu, seuls les votes solennels sont enregistrés avec l'intégralité des votes individuels.</p><p>Pour le reste de ces scrutins, seuls les députés ayant voté différemment de leur groupe politique sont signalés. Cela pourrait éventuellement permettra à terme d'évaluer par exemple la fidélité d'un député à son groupe. Nous vous recommandons cependant en attendant le site <a rel="nofollow" href="http://mon-depute.fr/">Mon-Depute.fr</a> qui présente ces données.</p>

  <h3 id="post_9">Pourquoi ne prenez-vous pas en compte le travail des députés dans leur circonscription ?</h3>
  <p>Comme son sous-titre l'indique, NosDéputés.fr se veut un observatoire de l'activité parlementaire. Comme le définit la Constitution, les députés ne sont pas élus selon le principe du mandat impératif et tout député est donc un élu de la Nation avant d'être celui de sa circonscription. C'est pourquoi nous nous intéressons exclusivement au travail législatif et de contrôle du gouvernement propres à la fonction parlementaire.</p><p>Nous conseillons aux visiteurs de ce site s'intéressant à l'activité en circonscription de leurs élus de consulter la presse régionale ou les sites/blogs des députés qui peuvent être une bonne source d'information.</p>

  <h3 id="post_10">Sur la page synthèse, pourquoi ne proposez-vous pas un classement des députés ?</h3>
  <p>Si nous proposons des outils permettant aux citoyens de se faire leur propre évaluation du travail de chacun dans différents domaines, nous sommes conscients de la complexité et de la richesse du travail parlementaire et nous n'estimons pas possible ni souhaitable de réaliser un véritable classement. De plus si nous limitons notre page de synthèse aux seuls députés en activité depuis plus de 10 mois, il reste que certains bilans sont effectivement proposés sur des durées plus courtes pour les députés ayant commencé leur mandat en cours de législature.</p><p>Il y a également des députés malades ou ayant des responsabilités extra-parlementaires qui peuvent expliquer une activité plus réduite à l'Assemblée nationale. Ces responsabilités sont indiquées sur la page dédiée à chaque député. Nous invitons donc les visiteurs à consulter toutes ces pages avant de faire des jugements hâtifs uniquement fondés sur la synthèse. Cette page ne peut être correctement appréhendée que comme une introduction à l'activité des députés.</p>

  <h3 id="post_31">Que représentent les « vacances parlementaires » indiquées sur les graphiques ?</h3>
  <p>L'agenda parlementaire prévoit chaque année quelques semaines de repos pour les députés. Nous signalons ces périodes en gris sur les graphiques d'activité des parlementaires. Il arrive cependant que les députés se réunissent tout de même lors de ces périodes, notamment pour des réunions de commission. Seules les semaines au cours desquelles aucune réunion ne s'est effectivement déroulée au Palais Bourbon sont indiquées en gris comme&nbsp;«&nbsp;vacances parlementaires&nbsp;».</p><p>Par ailleurs, au cours d'une législature, certains députés ne peuvent parfois plus siéger à l'Assemblée pour diverses raisons (nomination au gouvernement, mission gouvernementale prolongée, ...). Ils sont alors remplacés par leurs suppléants, puis reprennent parfois leur siège plus tard au cours de la législature. Ces périodes&nbsp;«&nbsp;hors-mandat&nbsp;» sont également grisées dans les graphiques d'activité du député, au même titre que les périodes dites de&nbsp;«&nbsp;vacances&nbsp;».</p>

  <h3 id="post_27">Que représentent les « semaines d'activité » indiquées pour chaque député ?</h3>
  <p>Les semaines d'activité sont calculées à partir de tous les éléments mesurables disponibles au Journal Officiel et sur le site de l'Assemblée nationale qui permettent de détecter la présence physique d'un parlementaire au sein du Palais Bourbon :</p><p>&nbsp;- la présence aux réunions de commissions (Journal Officiel),</p><p>&nbsp;- les prises de parole en commission ou en hémicycle (site de l'Assemblée nationale).</p><p>Si un parlementaire a été détecté via ces sources d'information comme présent au moins une fois pour une semaine considérée, nos algorithmes enregistrent sa présence pour la semaine.</p><p>Nous sommes conscients que les réunions de groupe, ainsi que les participations à divers travaux préparatoires ou une présence dans les bureaux de l'Assemblée ne sont pas visibles via ces sources d'informations, mais il est impossible de les prendre en compte sans source exhaustive. De même, nous ne pouvons prendre en compte la présence en hémicycle à partir des informations relatives aux votes car les informations publiées sont malheureusement incomplètes (pas de mention des délégations de vote, ni de tous les votants pour les scrutins publics). Nous encourageons régulièrement les services de l'Assemblée à publier les informations les plus précises et complètes possibles en la matière et nous utiliserons ces nouvelles sources d'information dès qu'elles seront disponibles.</p>

  <h3 id="post_28">Prenez-vous en compte tous les travaux effectués à l'Assemblée nationale ?</h3>
  <p>Nous prenons compte de l'ensemble des éléments publics, publiés, et exploitables, disponibles sur le site de l'Assemblée nationale et au Journal Officiel.</p><p>Les travaux des commissions d'enquête, des missions d'information ou encore des offices et délégations sont donc bien intégrés dans la mesure de leur disponibilité.</p><p>Faute de compte-rendu public ou exploitable, les réunions des groupes politiques et de nombreuses délégations ou missions parlementaires internationales restent en revanche difficiles à prendre en compte. Bien qu'elles représentent une charge de travail importante limitant naturellement la participation aux travaux à l'Assemblée, les missions confiées individuellement à quelques députés par le gouvernement ou l'Elysée ne sont malheureusement pas documentées et ne peuvent donc être prises en compte en l'état.</p><p>Nous encourageons régulièrement l'Assemblée nationale à publier plus largement et précisément ces informations afin de renforcer l'exhaustivité de notre observatoire de l'activité parlementaire.</p>

  <h3 id="post_32">Que représentent les nombres indiqués dans la barre « Activité » sur la page de chaque député ?</h3>
  <p>Pour les députés en activité, chaque nombre représente la valeur d'un indicateur (semaines d'activité, présences en commissions, interventions en commission, longues ou courtes en hémicycle, amendements, propositions, rapports et questions) mesuré sur les douze derniers mois. Ces chiffres peuvent donc évoluer, positivement ou négativement, jour après jour&nbsp;: il s'agit d'une synthèse glissante, un instantané de l'année écoulée. Lorsqu'un député exerce sa fonction depuis moins de 12 mois, les indicateurs synthétisent son activité depuis sa prise de fonction.</p><p>Si un député a exercé son mandat durant au moins dix des douze derniers mois, il est intégré à la <a rel="nofollow" href="/synthese">synthèse globale</a> permettant de comparer l'activité des députés sur l'année passée. Il serait en effet injuste et donc peu pertinent de comparer l'activité sur douze mois de la plupart des députés à l'activité de certains autres sur une durée réduite.</p><p>Si un indicateur du député se trouve parmi les 150 plus hauts ou plus bas chiffres de la synthèse globale, il apparaît respectivement en vert ou en rouge.</p><p>Lorsque le mandat d'un député est clos, celui-ci disparaît également de la synthèse globale. Les indicateurs sur sa page représentent alors les totaux sur l'ensemble de la durée de son ou ses mandats au cours de la législature. Il ne serait en effet pas pertinent de comparer les chiffres d'un député en activité à ceux d'un ancien député qui correspondraient à une période différente (dans le temps ou dans la longueur).</p>

  <h2 id="post_11">Les responsables du site</h2>

  <h3 id="post_12">Comment peut-on vous contacter ?</h3>
  <p>Vous pouvez écrire à l'ensemble des responsables du collectif <a rel="nofollow" href="http://www.regardscitoyens.org/">Regards Citoyens</a> à notre adresse de contact&nbsp;: contact [at] regardscitoyens.org</p>

  <h3 id="post_13">Etes vous affiliés à un parti ou à une organisation politique ?</h3>
  <p>Les activités du collectif <a rel="nofollow" href="http://regardscitoyens.org/">Regards Citoyens</a> et du site <a rel="nofollow" href="http://nosdeputes.fr/">NosDéputés.fr</a> sont totalement indépendantes de tout parti politique.</p>

  <h3 id="post_14">Avez-vous été payés pour réaliser ce site ?</h3>
  <p>Non, ce site a été entièrement réalisé par des bénévoles. Nous avons payé nous-mêmes l'ensemble de l'infrastructure technique et les quelques supports de communication nécessaires au lancement du site.</p>

  <h3 id="post_15">Quelles sont vos sources d'inspiration ?</h3>
  <p>Le pionnier de l'observation parlementaire en France est Olivier de Solan qui propose depuis des années sur son site <a rel="nofollow" href="http://mon-depute.fr/">Mon-Depute.fr</a> le relevé des votes des députés à partir des informations sur les scrutins publics. En France toujours, les sites <a rel="nofollow" href="http://www.laquadrature.net/wiki/Memoire_politique">Mémoire Politique</a> ainsi que <a rel="nofollow" href="http://www.candidats.fr/">Candidats.fr</a>, bien que présentant la seule vision de leurs créateurs, sont une autre grande source d'inspiration. Enfin, à l'étranger, les initiatives comme <a rel="nofollow" href="http://www.theyworkforyou.com/">TheyWorkForYou</a> en Grande-Bretagne, Parlorama.eu et <a rel="nofollow" href="http://votewatch.eu/">VoteWatch.eu</a> en Europe, ainsi que le travail de la <a rel="nofollow" href="http://www.sunlightfoundation.com/">Fondation Sunlight</a> aux États-Unis ont particulièrement retenu notre attention.</p>

  <h2 id="post_16">Utilisation du site</h2>

  <h3 id="post_17">Pourquoi dois-je créer un compte pour pouvoir déposer un commentaire ?</h3>
  <p>Nous avons choisi un système de modération à posteriori. N'étant pas responsables des propos tenus par nos utilisateurs dans ces commentaires, la loi nous oblige au vu de notre statut d'hébergeur à être capable d'identifier la ou les personnes responsables d'éventuels propos diffamatoires sur le site. C'est donc pour offir des conditions de débats optimales tout en se conformant à la loi que nous avons choisi ce système. Pour autant, vous pourrez noter que l'inscription est d'une très grande simplicité.</p>

  <h3 id="post_18">Comment puis-je relayer mon activité du site sur mes réseaux sociaux ?</h3>
  <p>Comme vous avez pu le constater, nous publions un certain nombre de flux RSS qui vous permettent de suivre en temps réel l'activité des députés que vous choisissez, ainsi que l'activité générale du site. Vous pouvez les utiliser pour relayer votre activité ou celle de votre député sur Twitter ou Identica via le site <a rel="nofollow" href="http://twitterfeed.com/">TwitterFeed</a>. Facebook propose également ce type d'outils.</p>

  <h3 id="post_19">Comment puis-je vous aider ?</h3>
  <p>La meilleure manière de nous aider est de vous exprimer dans les commentaires sur les travaux parlementaires et ainsi de contribuer aux débats qui ont lieu sur le site. Vous pouvez également enrichir la qualité des données en nous aidant à mettre en valeur les interventions utiles au débat en signalant les commentaires jugés non-constructifs. Si vous le souhaitez, vous pouvez aussi nous aider à populariser le site en en parlant autour de vous. Vous pouvez également <a rel="nofollow" href="http://www.regardscitoyens.org/nous-aider/">nous soutenir financièrement</a>, pour faire face aux coûts de mise en place de ce site. Enfin, si vous avez des idées d'amélioration ou des compétences particulières que vous estimez utiles à notre travail, n'hésitez à nous en faire part également par e-mail!</p>

  <h3 id="post_21">Proposez-vous un suivi par alertes e-mails ?</h3>
  <p>Oui. Depuis la page de chaque député, vous pouvez vous abonner pour recevoir par mail au rythme que vous souhaitez les informations relatives à l'activité de ce parlementaire.</p><p>Nous proposons également un système d'alerte par mots clés depuis le moteur de recherche. Pour ce faire, il suffit de cliquer sur l’icône représentant un courriel et d'indiquer votre adresse e-mail.</p>

  <h3 id="post_33">Quelles sont les rêgles d'encadrement des commentaires ?</h3>
  <p>Regards Citoyens a à coeur de permettre à tous de s'exprimer. C'est dans cet objectif que nous faisons le choix d'une modération a posteriori uniquement des commentaires publiés par les citoyens et qui en portent la responsabilité individuelle. Cependant, afin d'assurer un débat constructif, il convient de respecter certaines règles de bienséance :</p><p>- respecter la loi, et donc refuser tout propos raciste, incitation à la haine ou à la violence, ou qui puisse porter atteinte à l'intégrité ou diffamer autrui ;</p><p>- commenter l'objet des travaux lui-même et non sur tout autre sujet sans lien ;</p><p>- éviter à tout prix le copier/coller du même argumentaire d'un commentaire à l'autre.</p><p>En cas de manquement à ces règles, les commentaires pourront être supprimés et le compte de l'utilisateur suspendu de manière temporaire, voire définitive. En cas de conflit ou contestation, il est toujours possible de nous contacter à contact@regardscitoyens.org pour dialoguer.</p>

  <h2 id="post_20">Fonctionnalités techniques</h2>

  <h3 id="post_26">Est ce que ce site est un logiciel libre ?</h3>
  <p>Oui&nbsp;! Le code source du site et sa documentation technique sont disponibles sous <a rel="nofollow" href="http://www.gnu.org/licenses/agpl-3.0.html">licence AGPL</a> à l'adresse suivante&nbsp;: <a rel="nofollow" href="https://github.com/regardscitoyens/nosdeputes.fr">https://github.com/regardscitoyens/nosdeputes.fr</a></p><p>Si vous avez des talents de développeur, n'hésitez donc pas à venir nous aider&nbsp;!</p>

  <h3 id="post_22">Proposez-vous une API ?</h3>
  <p>Une API a été développée. Elle est encore en version Beta. Vous trouverez sa documentation sur la page suivante&nbsp;: <a rel="nofollow" href="https://github.com/regardscitoyens/nosdeputes.fr/blob/master/doc/api.md">https://github.com/regardscitoyens/nosdeputes.fr/blob/master/doc/api.md</a></p>

  <h3 id="post_23">Quelles sont les technologies employées pour la création ce site ?</h3>
  <p>Le site a été construit et est hébergé entièrement avec des logiciels libres. Notre serveur est une machine GNU/Linux Debian utilisant les services Apache 2 et MySQL. Le site a été développé en PHP grâce à l'environnement de développement <a rel="nofollow" href="http://www.symfony-project.org/">Symfony</a>. Nous utilisons la librairie <a rel="nofollow" href="http://www.pchart.net/">pChart</a> et <a rel="nofollow" href="https://d3js.org/">d3.js</a> pour tracer les graphiques, gd pour le traitement d'image et jQuery et jQuery-ui pour la surcouche javascript (ainsi que les plugins <a rel="nofollow" href="http://davidlynch.org/projects/maphilight/docs/">mapHighlight</a> et <a rel="nofollow" href="https://www.dynatable.com">DynaTable</a>). Nous nous efforçons de respecter les standards définis par le W3C. Si vous avez des problèmes d'accessibilité, n'hésitez pas à nous le signaler à contact [at] regardscitoyens.org et nous ferons le maximum pour les corriger rapidement.</p>
</div>
