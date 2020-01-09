<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Nomenclature\Senator;
use AppBundle\Entity\Nomenclature\SenatorArea;
use AppBundle\ValueObject\Genders;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNomenclatureSenatorData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ($zones = $this->createSenatorAreas() as $zone) {
            $manager->persist($zone);
        }

        $senator001 = $this->createSenator(
            $zones['0001'],
            "Troisième circonscription de l'Ain",
            Genders::MALE,
            'Alban',
            'Martin',
            null,
            'alban-martin'
        );
        $senator001->setEmailAddress('alban.martin@en-marche-dev.fr');
        $senator001->setFacebookPageUrl('https://www.facebook.com/albanmartin-fake');
        $senator001->setTwitterPageUrl('https://twitter.com/albanmartin-fake');

        $manager->persist($senator001);

        $manager->flush();
    }

    private function createSenator(
        SenatorArea $area,
        string $areaLabel,
        string $gender,
        string $firstName,
        string $lastName,
        string $geojson = null,
        string $slug = null,
        string $status = Senator::ENABLED
    ): Senator {
        $directory = __DIR__.'/../../DataFixtures/legislatives';
        $description = sprintf('%s/description.txt', $directory);

        $senator = new Senator();
        $senator->setGender($gender);
        $senator->setFirstName($firstName);
        $senator->setLastName($lastName);
        $senator->setArea($area);
        $senator->setAreaLabel($areaLabel);
        $senator->setGeojson($geojson);
        $senator->setDescription(file_get_contents($description));
        $senator->setStatus($status);

        if ($slug) {
            $senator->setSlug($slug);
        }

        return $senator;
    }

    /**
     * @return SenatorArea[]
     */
    private function createSenatorAreas(): array
    {
        return [
            // France Métropolitaine
            '0001' => SenatorArea::createDepartmentZone('01', 'Ain', ['01']),
            '0002' => SenatorArea::createDepartmentZone('02', 'Aisne', ['02']),
            '0019' => SenatorArea::createDepartmentZone('19', 'Corrèze', ['19']),
            '002A' => SenatorArea::createDepartmentZone('2A', 'Corse Sud', ['20', '2A', '2B', 'Corse']),
            '002B' => SenatorArea::createDepartmentZone('2B', 'Haute Corse', ['20', '2A', '2B', 'Corse']),
            '0021' => SenatorArea::createDepartmentZone('21', "Côte d'Or", ['21']),
            '0073' => SenatorArea::createDepartmentZone('73', 'Savoie', ['73']),
            '0074' => SenatorArea::createDepartmentZone('74', 'Haute-Savoie', ['74', 'Haute Savoie']),
            '0075' => SenatorArea::createDepartmentZone('75', 'Paris', ['75']),
            '0092' => SenatorArea::createDepartmentZone('92', 'Hauts-de-Seine', ['92', 'Hauts de Seine']),

            // Outre-Mer
            '0971' => SenatorArea::createDepartmentZone('971', 'Guadeloupe', ['971']),
            '0972' => SenatorArea::createDepartmentZone('972', 'Martinique', ['972']),
            '0973' => SenatorArea::createDepartmentZone('973', 'Guyane', ['973']),
            '0974' => SenatorArea::createDepartmentZone('974', 'La Réunion', ['974']),
            '0975' => SenatorArea::createDepartmentZone('975', 'Saint-Pierre-et-Miquelon', ['975', 'Saint Pierre et Miquelon']),
            '0976' => SenatorArea::createDepartmentZone('976', 'Mayotte', ['976']),
            '0977' => SenatorArea::createDepartmentZone('977', 'Saint-Barthélemy', ['977', 'Saint Barthelemy']),
            '0978' => SenatorArea::createDepartmentZone('978', 'Saint-Martin', ['978', 'Saint Martin']),
            '0986' => SenatorArea::createDepartmentZone('986', 'Wallis-et-Futuna', ['986', 'Wallis et Futuna']),
            '0987' => SenatorArea::createDepartmentZone('987', 'Polynésie française', ['987']),
            '0988' => SenatorArea::createDepartmentZone('988', 'Nouvelle-Calédonie', ['988', 'Nouvelle Calédonie']),
            '0989' => SenatorArea::createDepartmentZone('989', 'Clipperton', ['989']),

            // Circonscriptions des français à l'étranger
            '1001' => SenatorArea::createRegionZone('1001', 'USA et Canada', ['US', 'CA', 'USA', 'CAN', 'États-Unis', 'Etats Unis', 'Canada']),
            '1002' => SenatorArea::createRegionZone('1002', 'Amériques et Caraïbes', [
                'Antigua-et-Barbuda',
                'Argentine',
                'Bahamas',
                'Barbade',
                'Belize',
                'Bolivie',
                'Brésil',
                'Chili',
                'Colombie',
                'Costa Rica',
                'Cuba',
                'République dominicaine',
                'Dominique',
                'Équateur',
                'Grenade',
                'Guatemala',
                'Guyana',
                'Haïti',
                'Honduras',
                'Jamaïque',
                'Mexique',
                'Nicaragua',
                'Panama',
                'Paraguay',
                'Pérou',
                'Saint-Christophe-et-Niévès',
                'Sainte-Lucie',
                'Saint-Vincent-et-les Grenadines',
                'Salvador',
                'Suriname',
                'Trinité-et-Tobago',
                'Uruguay',
                'Venezuela',
            ]),

            '1003' => SenatorArea::createRegionZone('1003', 'Europe du Nord et Pays Baltes', [
                'Danemark',
                'Estonie',
                'Finlande',
                'Irlande',
                'Islande',
                'Lettonie',
                'Lituanie',
                'Norvège',
                'Royaume-Uni',
                'Suède',
            ]),

            '1004' => SenatorArea::createRegionZone('1004', 'Bénélux', [
                'Belgique',
                'Luxembourg',
                'Pays-Bas',
            ]),

            '1005' => SenatorArea::createRegionZone('1005', 'Péninsule Ibérique et Monaco', [
                'Andorre',
                'Espagne',
                'Monaco',
                'Portugal',
            ]),

            '1006' => SenatorArea::createRegionZone('1006', 'Suisse et Liechtenstein', [
                'Suisse',
                'Liechtenstein',
            ]),

            '1007' => SenatorArea::createRegionZone('1007', 'Europe Centrale', [
                'Allemagne',
                'Albanie',
                'Autriche',
                'Bosnie-Herzégovine',
                'Bulgarie',
                'Croatie',
                'Hongrie',
                'Kosovo',
                'Macédoine',
                'Monténégro',
                'Pologne',
                'Roumanie',
                'Serbie',
                'Slovaquie',
                'Slovénie',
                'République tchèque',
            ]),

            '1008' => SenatorArea::createRegionZone('1008', 'Pourtour méditerranéen', [
                'Chypre',
                'Grèce',
                'Israël',
                'Italie',
                'Malte',
                'Saint-Martin',
                'Saint-Siège',
                'Turquie',
            ]),

            '1009' => SenatorArea::createRegionZone('1009', 'Afrique du Nord et Centrale', [
                'Algérie',
                'Burkina Faso',
                'Cap-Vert',
                'Côte d\'Ivoire',
                'Gambie',
                'Guinée',
                'Guinée-Bissau',
                'Liberia',
                'Libye',
                'Mali',
                'Maroc',
                'Mauritanie',
                'Niger',
                'Sénégal',
                'Sierra Leone',
                'Tunisie',
            ]),

            '1010' => SenatorArea::createRegionZone('1010', 'Afrique du Sud et Moyen Orient', [
                'Afrique du Sud',
                'Émirats Arabes Unis',
                'Oman',
                'Qatar',
                // ...
                'Zimbabwe',
            ]),

            '1011' => SenatorArea::createRegionZone('1011', 'Europe Orientale, Asie et Océanie', [
                // Europe Orientale
                'Arménie',
                'Azerbaïdjan',
                'Biélorussie',
                'Géorgie',
                'Moldavie',
                'Russie',
                'Ukraine',

                // Asie
                'Afghanistan',
                'Bangladesh',
                'Indonésie',
                'Chine',
                'Japon',
                // ...

                // Océanie
                'Australie',
                'Fidji',
                'Nouvelle-Zélande',
                // ...
                'Vanuatu',
            ]),
        ];
    }
}
