<?php

declare(strict_types=1);

/*
 * This file is part of the AstroBook project.
 * (c) David Pelletier-Ulrich <d@mztrix.me>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dogstronauts\AstroBook\Fixtures\Story;

use Dogstronauts\AstroBook\Events\Model\EventStatus;
use Dogstronauts\AstroBook\Fields\Model\FieldType;
use Dogstronauts\AstroBook\Fixtures\Factory\EventFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\FieldFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\ResourceTypeFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\TaxonomyFactory;
use Dogstronauts\AstroBook\Fixtures\Factory\UserFactory;
use Dogstronauts\AstroBook\Shared\Taxonomies\Model\Taxonomy;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\faker;

#[AsFixture(name: 'demo')]
final class DemoStory extends Story
{
    public function build(): void
    {
        // create users
        UserFactory::createSequence([
            ['identifier' => 'demo@demo.fr', 'plainPassword' => 'demo1234%'],
            ['identifier' => 'administrator', 'plainPassword' => 'administrator', 'roles' => ['ROLE_PLATFORM']],
        ]);

        // create few events
        EventFactory::new()->sequence([
            [
                'label' => 'Lunar Bone Expedition',
                'description' => 'Mission to explore and retrieve valuable artifacts from the lunar surface.',
                'startAt' => new \DateTimeImmutable(),
                'duration' => 11520, // 8 days
                'status' => faker()->randomElement(EventStatus::cases()),
            ],
            [
                'label' => 'Mars Fetch Mission',
                'description' => 'Long-term expedition to explore the red planet and retrieve Martian samples.',
                'startAt' => new \DateTimeImmutable('+9 days'),
                'duration' => 17280, // 12 days
                'status' => EventStatus::PUBLISHED,
            ],
            [
                'label' => 'Asteroid Belt Patrol',
                'description' => 'Security mission to monitor and protect the outer reaches of our solar system.',
                'startAt' => new \DateTimeImmutable('+21 days'),
                'duration' => 240, // 4 hours
                'status' => EventStatus::PUBLISHED,
            ],
            [
                'label' => 'Zero-G Tail Wag',
                'description' => 'Demonstration event where dogstronauts test their zero-gravity maneuvering and celebrate with a cosmic tail-wag.',
                'startAt' => new \DateTimeImmutable('+21 days'),
                'duration' => 120, // 2 hours
                'status' => EventStatus::PUBLISHED,
            ],
            [
                'label' => 'Galactic Kennel Conference',
                'description' => 'Interstellar summit gathering top space-trained canines to share findings on alien bones and bone-based propulsion.',
                'startAt' => new \DateTimeImmutable('+22 days'),
                'duration' => 420, // 7 hours
                'status' => EventStatus::DRAFT,
            ],
        ])->create();

        // Create parent taxonomies
        $species = TaxonomyFactory::createOne([
            'label' => 'Dog Species',
            'description' => 'Different dog species in the Dogstronauts universe',
        ]);

        $ranks = TaxonomyFactory::createOne([
            'label' => 'Space Ranks',
            'description' => 'Hierarchy of ranks in the Dogstronauts space program',
        ]);

        $missions = TaxonomyFactory::createOne([
            'label' => 'Space Missions',
            'description' => 'Types of missions in the Dogstronauts space program',
        ]);

        // Create child taxonomies
        $this->createChildTaxonomies($species, [
            'Labrador Astronaut' => 'Brave and loyal space explorers known for their retrieval skills.',
            'German Shepherd Navigator' => 'Intelligent and disciplined navigators with exceptional spatial awareness.',
            'Beagle Explorer' => 'Curious and determined explorers with a keen sense of discovery.',
        ]);

        $this->createChildTaxonomies($ranks, [
            'Space Cadet' => 'Entry-level position for aspiring space dogs.',
            'Mission Specialist' => 'Specialized role focusing on specific mission aspects.',
            'Flight Commander' => 'Leadership position responsible for mission success.',
            'Space Admiral' => 'Highest rank in the Dogstronauts space program.',
        ]);

        $this->createChildTaxonomies($missions, [
            'Lunar Bone Expedition' => 'Mission to explore and retrieve valuable artifacts from the lunar surface.',
            'Mars Fetch Mission' => 'Long-term expedition to explore the red planet and retrieve Martian samples.',
            'Asteroid Belt Patrol' => 'Security mission to monitor and protect the outer reaches of our solar system.',
        ]);

        // Create resource types with predefined fields
        ResourceTypeFactory::new()->sequence([
            [
                'label' => 'Mission Report',
                'description' => 'Detailed report format for post-mission analysis',
                'fields' => FieldFactory::new()->sequence([
                    [
                        'type' => FieldType::STRING,
                        'label' => 'Commander Name',
                        'options' => [
                            'placeholder' => 'e.g. Rex Thunderpaw',
                            'help' => 'Name of the commanding dogstronaut for this mission',
                        ],
                    ],
                    [
                        'type' => FieldType::DATETIME,
                        'label' => 'Mission Start',
                        'options' => [
                            'placeholder' => 'Select launch datetime',
                            'help' => 'Precise datetime of mission liftoff',
                        ],
                    ],
                    [
                        'type' => FieldType::BOOL,
                        'label' => 'Mission Success',
                        'options' => [
                            'placeholder' => 'Select yes or no',
                            'help' => 'Was the mission completed successfully?',
                        ],
                    ],
                    [
                        'type' => FieldType::ARRAY,
                        'label' => 'Available Modules',
                        'options' => [
                            'availableValues' => [
                                'Navigation',
                                'Thermal Control',
                                'Life Support',
                                'Communication',
                                'Bone Analyzer',
                            ],
                            'placeholder' => 'Select modules involved',
                            'help' => 'List of modules used or available during the mission',
                        ],
                    ],
                ]),
            ],
            [
                'label' => 'Dogstronaut BioSheet',
                'description' => 'Official biodata sheet used for interstellar missions and kennel archival.',
                'fields' => FieldFactory::new()->sequence([
                    [
                        'type' => FieldType::STRING,
                        'label' => 'Callsign',
                        'options' => [
                            'placeholder' => 'e.g. Luna Barkstar',
                            'help' => 'Operational callsign of the dogstronaut',
                        ],
                    ],
                    [
                        'type' => FieldType::INT,
                        'label' => 'Bone Age (in Earth years)',
                        'options' => [
                            'placeholder' => 'e.g. 7',
                            'help' => 'Dogstronaut age at mission time',
                        ],
                    ],
                    [
                        'type' => FieldType::STRING,
                        'label' => 'Breed Classification',
                        'options' => [
                            'placeholder' => 'e.g. Shepherd Alpha-Class',
                            'help' => 'Genetic classification of the dogstronaut',
                        ],
                    ],
                    [
                        'type' => FieldType::DATE,
                        'label' => 'First Lift-off Date',
                        'options' => [
                            'placeholder' => 'Select first launch date',
                            'help' => 'Date of the first space mission of the dogstronaut',
                        ],
                    ],
                ]),
            ],
            [
                'label' => 'Telemetry Packet',
                'description' => 'Real-time data packets captured during a mission',
                'fields' => FieldFactory::new()->sequence([
                    [
                        'type' => FieldType::FLOAT,
                        'label' => 'Temperature',
                        'options' => [
                            'placeholder' => 'e.g. 23.5',
                            'help' => 'Temperature measured in Celsius during mission',
                        ],
                    ],
                    [
                        'type' => FieldType::ARRAY,
                        'label' => 'Sensor Values',
                        'options' => [
                            'availableValues' => ['O2', 'CO2', 'Pulse', 'Motion'],
                            'placeholder' => 'Select sensor outputs',
                            'help' => 'Sensor types recorded during mission activity',
                        ],
                    ],
                    [
                        'type' => FieldType::JSON,
                        'label' => 'Custom Metadata',
                        'options' => [
                            'placeholder' => '{"extra":"data"}',
                            'help' => 'Any additional structured metadata (JSON format)',
                        ],
                    ],
                ]),
            ],
        ])->create();
    }

    /**
     * Creates child taxonomies for a parent taxonomy.
     *
     * @param Taxonomy              $parent              The parent taxonomy
     * @param array<string, string> $labelDescriptionMap Map of labels to descriptions
     */
    private function createChildTaxonomies(Taxonomy $parent, array $labelDescriptionMap): void
    {
        $attributes = [];
        foreach ($labelDescriptionMap as $label => $description) {
            $attributes[] = [
                'parent' => $parent,
                'label' => $label,
                'description' => $description,
            ];
        }

        TaxonomyFactory::createMany(count($labelDescriptionMap), function (int $i) use (&$attributes) {
            return $attributes[$i - 1];
        });
    }
}
