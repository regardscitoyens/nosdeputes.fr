<?php $plotarray = array('parlementaire' => $parlementaire, 'labels' => $labels, 'vacances' => $vacances,'n_participations' => $n_participations, 'n_presences' => $n_presences, 'n_mots' => $n_mots); ?>
<?php if (isset($options['questions'])) $plotarray = array_merge($plotarray, array('questions' => $options['questions'])); ?>
<?php if (isset($options['link'])) $plotarray = array_merge($plotarray, array('link' => $options['link'])); ?>
<?php if (!isset($options['plot'])) $options = array_merge($options, array('plot' => 'all')); ?>
<?php if ($options['plot'] == 'all' || $options['plot'] == 'total') echo include_component('plot', 'parlementairePresenceLastYearTotal', $plotarray); ?>
<?php if (isset($options['fonctions'])) $plotarray = array_merge($plotarray, array('fonctions' => $fonctions)); ?>
<?php if ($options['plot'] == 'all' || $options['plot'] == 'hemicycle') echo include_component('plot', 'parlementairePresenceLastYearHemicycle', $plotarray); ?>
<?php if ($options['plot'] == 'all' || $options['plot'] == 'commission') echo include_component('plot', 'parlementairePresenceLastYearCommission', $plotarray); ?>
