<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('max_execution_time', 0);

/*
	Convet excel file on open data given by Ministère de l'intérieur
*/

// convert_region();
// convert_departement();
convert_canton();
// convert_circonscription();

function convert_circonscription()
{
	$regions = [];

	$text = file_get_contents(__DIR__ . '/Legislatives_2017_Resultats_T1_Circonscription.csv');
	$lines = explode("\n", $text);

	foreach ($lines as $key => $line)
	{
		error_log($key);
		$fields = explode(";", $line);

		$regions[$fields[0]] = [
			'code_departement' => (int)$fields[0], // $code_region
			'libelle_departement' => str_replace('"', '', $fields[1]), // $libelle_region
			'code_circonscription' => (int)$fields[2], // $code_region
			'libelle_circonscription' => str_replace('"', '', $fields[3]), // $libelle_region
			'inscrits' => (int)$fields[4], // $inscrits
			'abstentions' => (int)$fields[5], // $abstentions
			'p_abs_ins' => floatval(str_replace(',', '.', $fields[6])), // $p_abs_ins
			'votants' => (int)$fields[7], // $votants
			'p_vot_ins' => floatval(str_replace(',', '.', $fields[8])), // $p_vot_ins
			'blancs' => (int)$fields[9], // $blancs
			'p_blancs_ins' => floatval(str_replace(',', '.', $fields[10])), // $p_blancs_ins
			'p_blancs_vot' => floatval(str_replace(',', '.', $fields[11])), // $p_blancs_vot
			'nuls' => (int)$fields[12], // $nuls
			'p_nuls_ins' => floatval(str_replace(',', '.', $fields[13])), // $p_nuls_ins
			'p_nuls_vot' => floatval(str_replace(',', '.', $fields[14])), // $p_nuls_vot
			'exprimes' => (int)$fields[15], // $exprimes
			'p_exp_ins' => floatval(str_replace(',', '.', $fields[16])), // $p_exp_ins
			'p_exp_vot' => floatval(str_replace(',', '.', $fields[17])), // $p_exp_vot
			'candidats' => []
		];

		$end = count($fields);
		error_log('end : ' . $end);

		$i = 18;
		while($i<$end)
		{
			error_log('nuance ' . $i);
			$num_panneau = str_replace('"', '', $fields[$i]);

			if(strlen($num_panneau)>0)
			{
				$regions[$fields[0]]['candidats'][] = [
					'num_panneau' => (int)$num_panneau,
					'sexe' => str_replace('"', '', $fields[$i+1]),
					'nom' => str_replace('"', '', $fields[$i+2]),
					'prenom' => str_replace('"', '', $fields[$i+3]),
					'nuance' => str_replace('"', '', $fields[$i+4]),
					'voix' => (int)$fields[$i+5],
					'p_voix_ins' => floatval(str_replace(',', '.', $fields[$i+6])),
					'p_voix_exp' => floatval(str_replace(',', '.', $fields[$i+7])),
					'sieges' => (int)$fields[$i+8],
				];
			}
			$i = $i + 9;
		}
		error_log('end line');
	}

	$filename = __DIR__ . '/circonscriptions.json';
	file_put_contents($filename, json_encode($regions, JSON_PRETTY_PRINT));
}

function convert_canton()
{
	$regions = [];

	$text = file_get_contents(__DIR__ . '/Legislatives_2017_Resultats_T1_Canton.csv');
	$lines = explode("\n", $text);

	foreach ($lines as $key => $line)
	{
		error_log($key);
		$fields = explode(";", $line);

		// $regions[str_replace('"', '', $fields[0]).$fields[2]] = [
		$region = [
			'code_departement' => str_replace('"', '', $fields[0]), // $code_region
			'libelle_departement' => str_replace('"', '', $fields[1]), // $libelle_region
			'code_canton' => format_code_canton($fields[2]), // $code_region
			'libelle_canton' => str_replace('"', '', $fields[3]), // $libelle_region
			'inscrits' => (int)$fields[4], // $inscrits
			'abstentions' => (int)$fields[5], // $abstentions
			'p_abs_ins' => floatval(str_replace(',', '.', $fields[6])), // $p_abs_ins
			'votants' => (int)$fields[7], // $votants
			'p_vot_ins' => floatval(str_replace(',', '.', $fields[8])), // $p_vot_ins
			'blancs' => (int)$fields[9], // $blancs
			'p_blancs_ins' => floatval(str_replace(',', '.', $fields[10])), // $p_blancs_ins
			'p_blancs_vot' => floatval(str_replace(',', '.', $fields[11])), // $p_blancs_vot
			'nuls' => (int)$fields[12], // $nuls
			'p_nuls_ins' => floatval(str_replace(',', '.', $fields[13])), // $p_nuls_ins
			'p_nuls_vot' => floatval(str_replace(',', '.', $fields[14])), // $p_nuls_vot
			'exprimes' => (int)$fields[15], // $exprimes
			'p_exp_ins' => floatval(str_replace(',', '.', $fields[16])), // $p_exp_ins
			'p_exp_vot' => floatval(str_replace(',', '.', $fields[17])), // $p_exp_vot
			'candidats' => []
		];

		$end = count($fields);

		$i = 18;
		while($i<$end)
		{
			$code_nuance = str_replace('"', '', $fields[$i]);
			if(strlen($code_nuance)>0)
			{
				// $regions[$fields[0]]['candidats'][] = [
				$region['candidats'][] = [
					'code_nuance' => $code_nuance,
					'voix' => (int)$fields[$i+1],
					'p_voix_ins' => floatval(str_replace(',', '.', $fields[$i+2])),
					'p_voix_exp' => floatval(str_replace(',', '.', $fields[$i+3])),
				];
			}
			$i = $i + 4;
		}

		$regions[] = $region;

		if($region['code_departement']=='38' && $region['code_canton']=='03')
		{
			error_log('found 3803');
		}
	}

	$filename = __DIR__ . '/cantons.json';
	file_put_contents($filename, json_encode($regions, JSON_PRETTY_PRINT));

	error_log('Nombre de cantons : ' . count($regions));
}

function format_code_canton($code)
{
	if(strlen($code)==1)
		return '0'.$code;
	else
		return $code;
}

function convert_departement()
{
	$regions = [];

	$text = file_get_contents(__DIR__ . '/Legislatives_2017_Resultats_T1_Departement.csv');
	$lines = explode("\n", $text);

	foreach ($lines as $key => $line)
	{
		error_log($key);
		$fields = explode(";", $line);

		$regions[$fields[0]] = [
			'code_departement' => (int)$fields[0], // $code_region
			'libelle_departement' => str_replace('"', '', $fields[1]), // $libelle_region
			'inscrits' => (int)$fields[2], // $inscrits
			'abstentions' => (int)$fields[3], // $abstentions
			'p_abs_ins' => floatval(str_replace(',', '.', $fields[4])), // $p_abs_ins
			'votants' => (int)$fields[5], // $votants
			'p_vot_ins' => floatval(str_replace(',', '.', $fields[6])), // $p_vot_ins
			'blancs' => (int)$fields[7], // $blancs
			'p_blancs_ins' => floatval(str_replace(',', '.', $fields[8])), // $p_blancs_ins
			'p_blancs_vot' => floatval(str_replace(',', '.', $fields[9])), // $p_blancs_vot
			'nuls' => (int)$fields[10], // $nuls
			'p_nuls_ins' => floatval(str_replace(',', '.', $fields[11])), // $p_nuls_ins
			'p_nuls_vot' => floatval(str_replace(',', '.', $fields[12])), // $p_nuls_vot
			'exprimes' => (int)$fields[13], // $exprimes
			'p_exp_ins' => floatval(str_replace(',', '.', $fields[14])), // $p_exp_ins
			'p_exp_vot' => floatval(str_replace(',', '.', $fields[15])), // $p_exp_vot
			'candidats' => []
		];

		$end = count($fields);
		error_log('end : ' . $end);

		$i = 16;
		while($i<$end)
		{
			error_log('nuance ' . $i);
			$code_nuance = str_replace('"', '', $fields[$i]);
			if(strlen($code_nuance)>0)
			{
				$regions[$fields[0]]['candidats'][] = [
					'code_nuance' => $code_nuance,
					'voix' => (int)$fields[$i+1],
					'p_voix_ins' => floatval(str_replace(',', '.', $fields[$i+2])),
					'p_voix_exp' => floatval(str_replace(',', '.', $fields[$i+3])),
					'sieges' => (int)$fields[$i+4],
				];
			}
			$i = $i + 5;
		}
		error_log('end line');
	}

	$filename = __DIR__ . '/departements.json';
	file_put_contents($filename, json_encode($regions, JSON_PRETTY_PRINT));
}

function convert_region()
{
	$regions = [];

	$text = file_get_contents(__DIR__ . '/Legislatives_2017_Resultats_T1_Region.csv');
	$lines = explode("\n", $text);

	foreach ($lines as $key => $line)
	{
		error_log($key);
		$fields = explode(";", $line);

		$regions[$fields[0]] = [
			'code_region' => (int)$fields[0], // $code_region
			'libelle_region' => str_replace('"', '', $fields[1]), // $libelle_region
			'inscrits' => (int)$fields[2], // $inscrits
			'abstentions' => (int)$fields[3], // $abstentions
			'p_abs_ins' => floatval(str_replace(',', '.', $fields[4])), // $p_abs_ins
			'votants' => (int)$fields[5], // $votants
			'p_vot_ins' => floatval(str_replace(',', '.', $fields[6])), // $p_vot_ins
			'blancs' => (int)$fields[7], // $blancs
			'p_blancs_ins' => floatval(str_replace(',', '.', $fields[8])), // $p_blancs_ins
			'p_blancs_vot' => floatval(str_replace(',', '.', $fields[9])), // $p_blancs_vot
			'nuls' => (int)$fields[10], // $nuls
			'p_nuls_ins' => floatval(str_replace(',', '.', $fields[11])), // $p_nuls_ins
			'p_nuls_vot' => floatval(str_replace(',', '.', $fields[12])), // $p_nuls_vot
			'exprimes' => (int)$fields[13], // $exprimes
			'p_exp_ins' => floatval(str_replace(',', '.', $fields[14])), // $p_exp_ins
			'p_exp_vot' => floatval(str_replace(',', '.', $fields[15])), // $p_exp_vot
			'candidats' => []
		];

		$end = count($fields);
		error_log('end : ' . $end);

		$i = 16;
		while($i<$end)
		{
			error_log('nuance ' . $i);
			$code_nuance = str_replace('"', '', $fields[$i]);
			if(strlen($code_nuance)>0)
			{
				$regions[$fields[0]]['candidats'][] = [
					'code_nuance' => $code_nuance,
					'voix' => (int)$fields[$i+1],
					'p_voix_ins' => floatval(str_replace(',', '.', $fields[$i+2])),
					'p_voix_exp' => floatval(str_replace(',', '.', $fields[$i+3])),
					'sieges' => (int)$fields[$i+4],
				];
			}
			$i = $i + 5;
		}
		error_log('end line');
	}

	$filename = __DIR__ . '/regions.json';
	file_put_contents($filename, json_encode($regions, JSON_PRETTY_PRINT));
}