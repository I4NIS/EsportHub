<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Game;
use App\Models\GameMatch;
use App\Models\MatchMap;
use App\Models\Player;
use App\Models\PlayerStat;
use App\Models\Team;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (Game::exists()) {
            return;
        }

        $valorant = Game::create([
            'name' => 'Valorant',
            'slug' => 'valorant',
            'logo_url' => 'https://placehold.co/200x200?text=Valorant',
        ]);

        $cs2 = Game::create([
            'name' => 'Counter-Strike 2',
            'slug' => 'counter-strike-2',
            'logo_url' => 'https://placehold.co/200x200?text=CS2',
        ]);

        // --- Teams ---
        $valorantTeamsData = [
            ['name' => 'Sentinels',   'region' => 'NA',   'rank' => 1, 'earnings' => 3200000],
            ['name' => 'LOUD',        'region' => 'SA',   'rank' => 2, 'earnings' => 2800000],
            ['name' => 'Team Liquid', 'region' => 'EU',   'rank' => 3, 'earnings' => 2600000],
            ['name' => 'Fnatic',      'region' => 'EU',   'rank' => 4, 'earnings' => 2400000],
            ['name' => 'Paper Rex',   'region' => 'APAC', 'rank' => 5, 'earnings' => 2100000],
            ['name' => 'NRG Esports', 'region' => 'NA',   'rank' => 6, 'earnings' => 1900000],
            ['name' => '100 Thieves', 'region' => 'NA',   'rank' => 7, 'earnings' => 1700000],
            ['name' => 'G2 Esports',  'region' => 'EU',   'rank' => 8, 'earnings' => 1600000],
        ];

        $cs2TeamsData = [
            ['name' => 'Natus Vincere',  'region' => 'EU', 'rank' => 1, 'earnings' => 8500000],
            ['name' => 'FaZe Clan',      'region' => 'EU', 'rank' => 2, 'earnings' => 7200000],
            ['name' => 'Astralis',       'region' => 'EU', 'rank' => 3, 'earnings' => 6100000],
            ['name' => 'Team Vitality',  'region' => 'EU', 'rank' => 4, 'earnings' => 5500000],
            ['name' => 'Cloud9',         'region' => 'NA', 'rank' => 5, 'earnings' => 4900000],
            ['name' => 'MOUZ',           'region' => 'EU', 'rank' => 6, 'earnings' => 4200000],
            ['name' => 'ENCE',           'region' => 'EU', 'rank' => 7, 'earnings' => 3800000],
            ['name' => 'Team Liquid CS', 'region' => 'NA', 'rank' => 8, 'earnings' => 3400000],
        ];

        $vTeams = [];
        foreach ($valorantTeamsData as $data) {
            $vTeams[] = Team::create(array_merge($data, [
                'game_id'  => $valorant->id,
                'logo_url' => 'https://placehold.co/200x200?text=' . urlencode($data['name']),
            ]));
        }

        $cTeams = [];
        foreach ($cs2TeamsData as $data) {
            $cTeams[] = Team::create(array_merge($data, [
                'game_id'  => $cs2->id,
                'logo_url' => 'https://placehold.co/200x200?text=' . urlencode($data['name']),
            ]));
        }

        // --- Players (5 per team) ---
        $valorantPlayersData = [
            // Sentinels (0)
            ['pseudo' => 'TenZ',     'real_name' => 'Tyson Ngo',          'nationality' => 'CA', 'team' => 0],
            ['pseudo' => 'ShahZaM',  'real_name' => 'Shahzeeb Khan',       'nationality' => 'US', 'team' => 0],
            ['pseudo' => 'SicK',     'real_name' => 'Hunter Mims',         'nationality' => 'US', 'team' => 0],
            ['pseudo' => 'zekken',   'real_name' => 'Zachary Patrone',     'nationality' => 'US', 'team' => 0],
            ['pseudo' => 'Dapr',     'real_name' => 'Michael Gulino',      'nationality' => 'US', 'team' => 0],
            // LOUD (1)
            ['pseudo' => 'aspas',    'real_name' => 'Erick Santos',        'nationality' => 'BR', 'team' => 1],
            ['pseudo' => 'saadhak',  'real_name' => 'Matias Delipetro',    'nationality' => 'AR', 'team' => 1],
            ['pseudo' => 'Less',     'real_name' => 'Felipe Basso',        'nationality' => 'BR', 'team' => 1],
            ['pseudo' => 'pancada',  'real_name' => 'Bryan Luna',          'nationality' => 'BR', 'team' => 1],
            ['pseudo' => 'tuyz',     'real_name' => 'Arthur Lira',         'nationality' => 'BR', 'team' => 1],
            // Team Liquid (2)
            ['pseudo' => 'Leo',      'real_name' => 'Leonardo Slooby',     'nationality' => 'SE', 'team' => 2],
            ['pseudo' => 'nAts',     'real_name' => 'Ayaz Akhmetshin',     'nationality' => 'RU', 'team' => 2],
            ['pseudo' => 'Jamppi',   'real_name' => 'Elias Olkkonen',      'nationality' => 'FI', 'team' => 2],
            ['pseudo' => 'Nivera',   'real_name' => 'Nabil Benrlitom',     'nationality' => 'BE', 'team' => 2],
            ['pseudo' => 'cNed',     'real_name' => 'Mehmet Yağız İpek',  'nationality' => 'TR', 'team' => 2],
            // Fnatic (3)
            ['pseudo' => 'Alfajer',  'real_name' => 'Emir Beder',          'nationality' => 'TR', 'team' => 3],
            ['pseudo' => 'Derke',    'real_name' => 'Nikita Sirmitev',     'nationality' => 'FI', 'team' => 3],
            ['pseudo' => 'Boaster',  'real_name' => 'Jake Howlett',        'nationality' => 'GB', 'team' => 3],
            ['pseudo' => 'Enzo',     'real_name' => 'Enzo Mestari',        'nationality' => 'FR', 'team' => 3],
            ['pseudo' => 'Chronicle','real_name' => 'Timofey Khromov',     'nationality' => 'RU', 'team' => 3],
            // Paper Rex (4)
            ['pseudo' => 'f0rsakeN', 'real_name' => 'Jason Susanto',       'nationality' => 'SG', 'team' => 4],
            ['pseudo' => 'mindfreak','real_name' => 'Aaron Leonhart',      'nationality' => 'AU', 'team' => 4],
            ['pseudo' => 'Jinggg',   'real_name' => 'Wang Jing Jie',       'nationality' => 'SG', 'team' => 4],
            ['pseudo' => 'd4v41',    'real_name' => 'Khalish Rusyaidee',   'nationality' => 'SG', 'team' => 4],
            ['pseudo' => 'Benkai',   'real_name' => 'Benedict Tan',        'nationality' => 'SG', 'team' => 4],
            // NRG (5)
            ['pseudo' => 'crashies', 'real_name' => 'Austin Roberts',      'nationality' => 'US', 'team' => 5],
            ['pseudo' => 'Victor',   'real_name' => 'Victor Wong',         'nationality' => 'US', 'team' => 5],
            ['pseudo' => 'ardiis',   'real_name' => 'Ardis Svarenieks',    'nationality' => 'LV', 'team' => 5],
            ['pseudo' => 'eeiu',     'real_name' => 'Daniel Vucak',        'nationality' => 'AU', 'team' => 5],
            ['pseudo' => 'FNS',      'real_name' => 'Pujan Mehta',         'nationality' => 'CA', 'team' => 5],
            // 100 Thieves (6)
            ['pseudo' => 'Asuna',    'real_name' => 'Peter Mazuryk',       'nationality' => 'US', 'team' => 6],
            ['pseudo' => 'bang',     'real_name' => 'Sean Bezerra',        'nationality' => 'US', 'team' => 6],
            ['pseudo' => 'Stellar',  'real_name' => 'Bryce Meyrink',       'nationality' => 'US', 'team' => 6],
            ['pseudo' => 'Cryo',     'real_name' => 'Robert Huang',        'nationality' => 'US', 'team' => 6],
            ['pseudo' => 'Derrek',   'real_name' => 'Derrek Ha',           'nationality' => 'US', 'team' => 6],
            // G2 (7)
            ['pseudo' => 'mixwell',  'real_name' => 'Oscar Cañellas',     'nationality' => 'ES', 'team' => 7],
            ['pseudo' => 'hoody',    'real_name' => 'Aaro Peltokangas',    'nationality' => 'FI', 'team' => 7],
            ['pseudo' => 'M1XER',    'real_name' => 'Mert Yanık',         'nationality' => 'TR', 'team' => 7],
            ['pseudo' => 'valyn',    'real_name' => 'Jacob Batio',         'nationality' => 'US', 'team' => 7],
            ['pseudo' => 'leaf',     'real_name' => 'Nathan Orf',          'nationality' => 'US', 'team' => 7],
        ];

        $cs2PlayersData = [
            // NaVi (0)
            ['pseudo' => 's1mple',   'real_name' => 'Oleksandr Kostyliev', 'nationality' => 'UA', 'team' => 0],
            ['pseudo' => 'electronic','real_name' => 'Denis Sharipov',     'nationality' => 'RU', 'team' => 0],
            ['pseudo' => 'b1t',      'real_name' => 'Valerii Vakhovskyi',  'nationality' => 'UA', 'team' => 0],
            ['pseudo' => 'Perfecto', 'real_name' => 'Ilya Zalutskiy',      'nationality' => 'RU', 'team' => 0],
            ['pseudo' => 'sdy',      'real_name' => 'Abdulkhalik Guseynov','nationality' => 'RU', 'team' => 0],
            // FaZe (1)
            ['pseudo' => 'rain',     'real_name' => 'Håvard Nygaard',     'nationality' => 'NO', 'team' => 1],
            ['pseudo' => 'karrigan', 'real_name' => 'Finn Andersen',       'nationality' => 'DK', 'team' => 1],
            ['pseudo' => 'broky',    'real_name' => 'Helvijs Saukants',    'nationality' => 'LV', 'team' => 1],
            ['pseudo' => 'ropz',     'real_name' => 'Robin Kool',          'nationality' => 'EE', 'team' => 1],
            ['pseudo' => 'twistzz',  'real_name' => 'Russel Van Dulken',   'nationality' => 'CA', 'team' => 1],
            // Astralis (2)
            ['pseudo' => 'gla1ve',   'real_name' => 'Lukas Rossander',     'nationality' => 'DK', 'team' => 2],
            ['pseudo' => 'dev1ce',   'real_name' => 'Nicolai Reedtz',      'nationality' => 'DK', 'team' => 2],
            ['pseudo' => 'dupreeh',  'real_name' => 'Peter Rasmussen',     'nationality' => 'DK', 'team' => 2],
            ['pseudo' => 'Xyp9x',    'real_name' => 'Andreas Højsleth',   'nationality' => 'DK', 'team' => 2],
            ['pseudo' => 'Lucky',    'real_name' => 'Joakim Lund Jørgensen','nationality' => 'DK', 'team' => 2],
            // Vitality (3)
            ['pseudo' => 'ZywOo',    'real_name' => 'Mathieu Herbaut',     'nationality' => 'FR', 'team' => 3],
            ['pseudo' => 'apEX',     'real_name' => 'Dan Madesclaire',     'nationality' => 'FR', 'team' => 3],
            ['pseudo' => 'misutaaa', 'real_name' => 'Bryan Mancel',        'nationality' => 'FR', 'team' => 3],
            ['pseudo' => 'flameZ',   'real_name' => 'Shahar Shushan',      'nationality' => 'IL', 'team' => 3],
            ['pseudo' => 'Spinx',    'real_name' => 'Lotan Giladi',        'nationality' => 'IL', 'team' => 3],
            // Cloud9 (4)
            ['pseudo' => 'ax1Le',    'real_name' => 'Sergey Rykhtorov',    'nationality' => 'KZ', 'team' => 4],
            ['pseudo' => 'Buster',   'real_name' => 'Timur Tulepov',       'nationality' => 'KZ', 'team' => 4],
            ['pseudo' => 'HObbit',   'real_name' => 'Abay Khasenov',       'nationality' => 'KZ', 'team' => 4],
            ['pseudo' => 'n0rb3r7',  'real_name' => 'Norbert Ágoston',    'nationality' => 'HU', 'team' => 4],
            ['pseudo' => 'Perfecto', 'real_name' => 'Ilya Zalutskiy',      'nationality' => 'RU', 'team' => 4],
            // MOUZ (5)
            ['pseudo' => 'sh1ro',    'real_name' => 'Dmitry Sokolov',      'nationality' => 'RU', 'team' => 5],
            ['pseudo' => 'xertioN',  'real_name' => 'Dorian Berman',       'nationality' => 'IL', 'team' => 5],
            ['pseudo' => 'torzsi',   'real_name' => 'Márton Törzsök',     'nationality' => 'HU', 'team' => 5],
            ['pseudo' => 'siuhy',    'real_name' => 'Frederik Gyldstrand', 'nationality' => 'DK', 'team' => 5],
            ['pseudo' => 'JDC',      'real_name' => 'Jon de Castro',       'nationality' => 'ES', 'team' => 5],
            // ENCE (6)
            ['pseudo' => 'Snappi',   'real_name' => 'Marco Pfeiffer',      'nationality' => 'DK', 'team' => 6],
            ['pseudo' => 'gla1ve',   'real_name' => 'Lukas Rossander',     'nationality' => 'DK', 'team' => 6],
            ['pseudo' => 'dycha',    'real_name' => 'Damian Dyche',        'nationality' => 'PL', 'team' => 6],
            ['pseudo' => 'hades',    'real_name' => 'Nemanja Bukvić',     'nationality' => 'RS', 'team' => 6],
            ['pseudo' => 'NertZ',    'real_name' => 'Daniel Senft',        'nationality' => 'DE', 'team' => 6],
            // Liquid CS (7)
            ['pseudo' => 'NAF',      'real_name' => 'Keith Markovic',      'nationality' => 'CA', 'team' => 7],
            ['pseudo' => 'EliGE',    'real_name' => 'Jonathan Jablonowski','nationality' => 'US', 'team' => 7],
            ['pseudo' => 'nitr0',    'real_name' => 'Nick Cannella',       'nationality' => 'US', 'team' => 7],
            ['pseudo' => 'oSee',     'real_name' => 'Owen Stown',          'nationality' => 'US', 'team' => 7],
            ['pseudo' => 'cadiaN',   'real_name' => 'Casper Møller',      'nationality' => 'DK', 'team' => 7],
        ];

        $vPlayers = [];
        foreach ($valorantPlayersData as $data) {
            $vPlayers[] = Player::create([
                'game_id'         => $valorant->id,
                'current_team_id' => $vTeams[$data['team']]->id,
                'pseudo'          => $data['pseudo'],
                'real_name'       => $data['real_name'],
                'nationality'     => $data['nationality'],
                'photo_url'       => 'https://placehold.co/200x200?text=' . urlencode($data['pseudo']),
            ]);
        }

        $cPlayers = [];
        foreach ($cs2PlayersData as $data) {
            $cPlayers[] = Player::create([
                'game_id'         => $cs2->id,
                'current_team_id' => $cTeams[$data['team']]->id,
                'pseudo'          => $data['pseudo'],
                'real_name'       => $data['real_name'],
                'nationality'     => $data['nationality'],
                'photo_url'       => 'https://placehold.co/200x200?text=' . urlencode($data['pseudo']),
            ]);
        }

        // --- Events ---
        $vChampions = Event::create([
            'game_id' => $valorant->id, 'name' => 'VALORANT Champions 2024',
            'logo_url' => 'https://placehold.co/200x200?text=Champions2024',
            'prize_pool' => '$2,250,000', 'start_date' => '2024-08-01', 'end_date' => '2024-08-25', 'status' => 'completed',
        ]);
        $vMasters = Event::create([
            'game_id' => $valorant->id, 'name' => 'VCT Masters Shanghai 2024',
            'logo_url' => 'https://placehold.co/200x200?text=Masters',
            'prize_pool' => '$500,000', 'start_date' => '2024-05-15', 'end_date' => '2024-06-02', 'status' => 'completed',
        ]);
        $vKickoff = Event::create([
            'game_id' => $valorant->id, 'name' => 'VCT 2025 Kickoff',
            'logo_url' => 'https://placehold.co/200x200?text=Kickoff2025',
            'prize_pool' => '$100,000', 'start_date' => '2025-01-15', 'end_date' => '2025-06-30', 'status' => 'ongoing',
        ]);
        $cMajor = Event::create([
            'game_id' => $cs2->id, 'name' => 'PGL CS2 Major Copenhagen 2024',
            'logo_url' => 'https://placehold.co/200x200?text=Major2024',
            'prize_pool' => '$1,250,000', 'start_date' => '2024-03-17', 'end_date' => '2024-03-31', 'status' => 'completed',
        ]);
        $cBlast = Event::create([
            'game_id' => $cs2->id, 'name' => 'BLAST Premier World Final 2024',
            'logo_url' => 'https://placehold.co/200x200?text=BLAST2024',
            'prize_pool' => '$1,000,000', 'start_date' => '2024-12-11', 'end_date' => '2025-06-30', 'status' => 'ongoing',
        ]);

        // --- Matches ---

        // Valorant completed
        $m1 = GameMatch::create([
            'event_id' => $vChampions->id, 'team1_id' => $vTeams[0]->id, 'team2_id' => $vTeams[1]->id,
            'score_team1' => 2, 'score_team2' => 1, 'status' => 'completed', 'scheduled_at' => '2024-08-24 18:00:00',
        ]);
        $m2 = GameMatch::create([
            'event_id' => $vChampions->id, 'team1_id' => $vTeams[2]->id, 'team2_id' => $vTeams[3]->id,
            'score_team1' => 1, 'score_team2' => 2, 'status' => 'completed', 'scheduled_at' => '2024-08-23 16:00:00',
        ]);
        $m3 = GameMatch::create([
            'event_id' => $vChampions->id, 'team1_id' => $vTeams[4]->id, 'team2_id' => $vTeams[5]->id,
            'score_team1' => 2, 'score_team2' => 0, 'status' => 'completed', 'scheduled_at' => '2024-08-22 14:00:00',
        ]);
        $m4 = GameMatch::create([
            'event_id' => $vMasters->id, 'team1_id' => $vTeams[6]->id, 'team2_id' => $vTeams[7]->id,
            'score_team1' => 0, 'score_team2' => 2, 'status' => 'completed', 'scheduled_at' => '2024-05-30 20:00:00',
        ]);
        $m5 = GameMatch::create([
            'event_id' => $vMasters->id, 'team1_id' => $vTeams[1]->id, 'team2_id' => $vTeams[3]->id,
            'score_team1' => 2, 'score_team2' => 1, 'status' => 'completed', 'scheduled_at' => '2024-05-28 18:00:00',
        ]);

        // Valorant live (couvre toutes les équipes)
        $m6 = GameMatch::create([
            'event_id' => $vKickoff->id, 'team1_id' => $vTeams[0]->id, 'team2_id' => $vTeams[4]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'live', 'scheduled_at' => now(),
        ]);
        $m7 = GameMatch::create([
            'event_id' => $vKickoff->id, 'team1_id' => $vTeams[2]->id, 'team2_id' => $vTeams[5]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'live', 'scheduled_at' => now(),
        ]);
        $m8 = GameMatch::create([
            'event_id' => $vKickoff->id, 'team1_id' => $vTeams[6]->id, 'team2_id' => $vTeams[1]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'live', 'scheduled_at' => now(),
        ]);

        // Valorant upcoming
        GameMatch::create([
            'event_id' => $vKickoff->id, 'team1_id' => $vTeams[3]->id, 'team2_id' => $vTeams[7]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming', 'scheduled_at' => now()->addDays(2),
        ]);
        GameMatch::create([
            'event_id' => $vKickoff->id, 'team1_id' => $vTeams[0]->id, 'team2_id' => $vTeams[2]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming', 'scheduled_at' => now()->addDays(5),
        ]);

        // CS2 completed
        $m9 = GameMatch::create([
            'event_id' => $cMajor->id, 'team1_id' => $cTeams[0]->id, 'team2_id' => $cTeams[1]->id,
            'score_team1' => 2, 'score_team2' => 0, 'status' => 'completed', 'scheduled_at' => '2024-03-30 15:00:00',
        ]);
        $m10 = GameMatch::create([
            'event_id' => $cMajor->id, 'team1_id' => $cTeams[2]->id, 'team2_id' => $cTeams[3]->id,
            'score_team1' => 1, 'score_team2' => 2, 'status' => 'completed', 'scheduled_at' => '2024-03-29 13:00:00',
        ]);
        $m11 = GameMatch::create([
            'event_id' => $cMajor->id, 'team1_id' => $cTeams[4]->id, 'team2_id' => $cTeams[5]->id,
            'score_team1' => 2, 'score_team2' => 1, 'status' => 'completed', 'scheduled_at' => '2024-03-28 17:00:00',
        ]);
        $m12 = GameMatch::create([
            'event_id' => $cMajor->id, 'team1_id' => $cTeams[6]->id, 'team2_id' => $cTeams[7]->id,
            'score_team1' => 0, 'score_team2' => 2, 'status' => 'completed', 'scheduled_at' => '2024-03-27 11:00:00',
        ]);

        // CS2 live
        $m13 = GameMatch::create([
            'event_id' => $cBlast->id, 'team1_id' => $cTeams[3]->id, 'team2_id' => $cTeams[2]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'live', 'scheduled_at' => now(),
        ]);
        $m14 = GameMatch::create([
            'event_id' => $cBlast->id, 'team1_id' => $cTeams[0]->id, 'team2_id' => $cTeams[5]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'live', 'scheduled_at' => now(),
        ]);
        $m15 = GameMatch::create([
            'event_id' => $cBlast->id, 'team1_id' => $cTeams[1]->id, 'team2_id' => $cTeams[6]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'live', 'scheduled_at' => now(),
        ]);

        // CS2 upcoming
        GameMatch::create([
            'event_id' => $cBlast->id, 'team1_id' => $cTeams[4]->id, 'team2_id' => $cTeams[7]->id,
            'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming', 'scheduled_at' => now()->addDays(3),
        ]);

        // --- Match Maps ---
        MatchMap::create(['match_id' => $m1->id, 'map_name' => 'Ascent',     'map_number' => 1, 'team1_round' => 13, 'team2_round' => 9,  'status' => 'completed']);
        MatchMap::create(['match_id' => $m1->id, 'map_name' => 'Bind',       'map_number' => 2, 'team1_round' => 10, 'team2_round' => 13, 'status' => 'completed']);
        MatchMap::create(['match_id' => $m1->id, 'map_name' => 'Icebox',     'map_number' => 3, 'team1_round' => 13, 'team2_round' => 11, 'status' => 'completed']);
        $map2a = MatchMap::create(['match_id' => $m9->id, 'map_name' => 'de_mirage',  'map_number' => 1, 'team1_round' => 16, 'team2_round' => 9,  'status' => 'completed']);
        MatchMap::create(['match_id' => $m9->id, 'map_name' => 'de_inferno', 'map_number' => 2, 'team1_round' => 16, 'team2_round' => 12, 'status' => 'completed']);

        // --- Player Stats ---
        $this->seedPlayerStats($vPlayers, $m1, null, $vTeams);
        $this->seedPlayerStats($cPlayers, $m9, $map2a, $cTeams, false);

        // --- Transactions ---
        $this->seedTransactions($vPlayers, $vTeams);
        $this->seedTransactions($cPlayers, $cTeams);

        // --- Users ---
        $testUser = User::create([
            'firstname'  => 'John',
            'lastname'   => 'Doe',
            'email'      => 'john@esporthub.test',
            'password'   => Hash::make('Password1'),
            'username'   => 'johndoe',
            'birthdate'  => '1998-06-15',
            'is_active'  => true,
        ]);
        User::create([
            'firstname'  => 'Admin',
            'lastname'   => 'EsportHub',
            'email'      => 'admin@esporthub.test',
            'password'   => Hash::make('AdminPassword1'),
            'username'   => 'admin',
            'birthdate'  => '1990-01-01',
            'is_active'  => true,
            'role'       => 'admin',
        ]);

        $testUser->likedTeams()->attach($vTeams[0]->id, ['liked_at' => now()]);
        $testUser->likedTeams()->attach($cTeams[0]->id, ['liked_at' => now()]);
        $testUser->followedPlayers()->attach($vPlayers[0]->id, ['followed_at' => now()]);
        $testUser->followedPlayers()->attach($cPlayers[0]->id, ['followed_at' => now()]);

        User::factory(5)->create();
    }

    private function seedPlayerStats(array $players, GameMatch $match, ?MatchMap $map, array $teams, bool $isValorant = true): void
    {
        $groups = [array_slice($players, 0, 5), array_slice($players, 5, 5)];
        foreach ($groups as $i => $group) {
            foreach ($group as $player) {
                PlayerStat::create([
                    'player_id'      => $player->id,
                    'match_id'       => $match->id,
                    'match_map_id'   => $map?->id,
                    'team_id'        => $teams[$i]->id,
                    'region'         => $teams[$i]->region,
                    'rating'         => round(fake()->randomFloat(2, 0.8, 1.8), 2),
                    'acs'            => round(fake()->randomFloat(1, 150, 320), 1),
                    'kd_ratio'       => round(fake()->randomFloat(2, 0.7, 2.2), 2),
                    'kast'           => round(fake()->randomFloat(2, 55, 90), 2),
                    'adr'            => round(fake()->randomFloat(1, 80, 200), 1),
                    'kpr'            => round(fake()->randomFloat(2, 0.5, 1.2), 2),
                    'headshot_pct'   => round(fake()->randomFloat(2, 15, 55), 2),
                    'clutch_pct'     => round(fake()->randomFloat(2, 5, 40), 2),
                ]);
            }
        }
    }

    private function seedTransactions(array $players, array $teams): void
    {
        foreach ($players as $index => $player) {
            $currentTeamIndex = (int) floor($index / 5);

            Transaction::create([
                'player_id'        => $player->id,
                'team_id'          => $teams[$currentTeamIndex]->id,
                'type'             => 'join',
                'transaction_date' => fake()->dateTimeBetween('-2 years', '-6 months')->format('Y-m-d'),
                'description'      => 'Signature officielle',
            ]);

            if ($index % 2 === 0 && $currentTeamIndex > 0) {
                Transaction::create([
                    'player_id'        => $player->id,
                    'team_id'          => $teams[$currentTeamIndex - 1]->id,
                    'type'             => 'leave',
                    'transaction_date' => fake()->dateTimeBetween('-4 years', '-2 years')->format('Y-m-d'),
                    'description'      => 'Fin de contrat',
                ]);
            }

            if ($index % 3 === 0 && $currentTeamIndex < count($teams) - 2) {
                Transaction::create([
                    'player_id'        => $player->id,
                    'team_id'          => $teams[$currentTeamIndex + 1]->id,
                    'type'             => 'join',
                    'transaction_date' => fake()->dateTimeBetween('-5 years', '-4 years')->format('Y-m-d'),
                    'description'      => 'Ancien club',
                ]);
                Transaction::create([
                    'player_id'        => $player->id,
                    'team_id'          => $teams[$currentTeamIndex + 1]->id,
                    'type'             => 'leave',
                    'transaction_date' => fake()->dateTimeBetween('-4 years', '-3 years')->format('Y-m-d'),
                    'description'      => 'Départ vers nouvelle équipe',
                ]);
            }
        }
    }
}
