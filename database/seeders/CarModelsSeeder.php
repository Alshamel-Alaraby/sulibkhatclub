<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CarRent\Entities\CarModel;

class CarModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carBrands = [
            ['name' => 'Camry', 'name_e' => 'Camry', 'brand_id' => 1],
            ['name' => 'Corolla', 'name_e' => 'Corolla', 'brand_id' => 1],
            ['name' => 'Prius', 'name_e' => 'Prius', 'brand_id' => 1],
            ['name' => 'RAV4', 'name_e' => 'RAV4', 'brand_id' => 1],
            ['name' => 'Highlander', 'name_e' => 'Highlander', 'brand_id' => 1],
            ['name' => 'Tacoma', 'name_e' => 'Tacoma', 'brand_id' => 1],
            ['name' => 'Tundra', 'name_e' => 'Tundra', 'brand_id' => 1],

            ['name' => 'Accord', 'name_e' => 'Accord', 'brand_id' => 2],
            ['name' => 'Civic', 'name_e' => 'Civic', 'brand_id' => 2],
            ['name' => 'CR-V', 'name_e' => 'CR-V', 'brand_id' => 2],
            ['name' => 'Pilot', 'name_e' => 'Pilot', 'brand_id' => 2],
            ['name' => 'HR-V', 'name_e' => 'HR-V', 'brand_id' => 2],
            ['name' => 'Odyssey', 'name_e' => 'Odyssey', 'brand_id' => 2],

            ['name' => 'F-150', 'name_e' => 'F-150', 'brand_id' => 3],
            ['name' => 'Mustang', 'name_e' => 'Mustang', 'brand_id' => 3],
            ['name' => 'Explorer', 'name_e' => 'Explorer', 'brand_id' => 3],
            ['name' => 'Escape', 'name_e' => 'Escape', 'brand_id' => 3],
            ['name' => 'Focus', 'name_e' => 'Focus', 'brand_id' => 3],
            ['name' => 'Fusion', 'name_e' => 'Fusion', 'brand_id' => 3],

            ['name' => 'Silverado', 'name_e' => 'Silverado', 'brand_id' => 4],
            ['name' => 'Equinox', 'name_e' => 'Equinox', 'brand_id' => 4],
            ['name' => 'Malibu', 'name_e' => 'Malibu', 'brand_id' => 4],
            ['name' => 'Traverse', 'name_e' => 'Traverse', 'brand_id' => 4],
            ['name' => 'Camaro', 'name_e' => 'Camaro', 'brand_id' => 4],

            ['name' => 'Golf', 'name_e' => 'Golf', 'brand_id' => 5],
            ['name' => 'Jetta', 'name_e' => 'Jetta', 'brand_id' => 5],
            ['name' => 'Passat', 'name_e' => 'Passat', 'brand_id' => 5],
            ['name' => 'Tiguan', 'name_e' => 'Tiguan', 'brand_id' => 5],
            ['name' => 'Atlas', 'name_e' => 'Atlas', 'brand_id' => 5],

            ['name' => 'Altima', 'name_e' => 'Altima', 'brand_id' => 6],
            ['name' => 'Rogue', 'name_e' => 'Rogue', 'brand_id' => 6],
            ['name' => 'Sentra', 'name_e' => 'Sentra', 'brand_id' => 6],
            ['name' => 'Pathfinder', 'name_e' => 'Pathfinder', 'brand_id' => 6],
            ['name' => 'Titan', 'name_e' => 'Titan', 'brand_id' => 6],

            ['name' => '3 Series', 'name_e' => '3 Series', 'brand_id' => 7],
            ['name' => '5 Series', 'name_e' => '5 Series', 'brand_id' => 7],
            ['name' => 'X3', 'name_e' => 'X3', 'brand_id' => 7],
            ['name' => 'X5', 'name_e' => 'X5', 'brand_id' => 7],
            ['name' => '7 Series', 'name_e' => '7 Series', 'brand_id' => 7],

            ['name' => 'C-Class', 'name_e' => 'C-Class', 'brand_id' => 8],
            ['name' => ' E-Class', 'name_e' => ' E-Class', 'brand_id' => 8],
            ['name' => 'S-Class', 'name_e' => 'S-Class', 'brand_id' => 8],
            ['name' => 'GLC', 'name_e' => 'GLC', 'brand_id' => 8],
            ['name' => 'GLE', 'name_e' => 'GLE', 'brand_id' => 8],

            ['name' => 'A3', 'name_e' => 'A3', 'brand_id' => 9],
            ['name' => ' A4', 'name_e' => ' A4', 'brand_id' => 9],
            ['name' => 'Q5', 'name_e' => 'Q5', 'brand_id' => 9],
            ['name' => 'Q7', 'name_e' => 'Q7', 'brand_id' => 9],
            ['name' => 'A6', 'name_e' => 'A6', 'brand_id' => 9],

            ['name' => 'Elantra', 'name_e' => 'Elantra', 'brand_id' => 10],
            ['name' => ' Sonata', 'name_e' => ' Sonata', 'brand_id' => 10],
            ['name' => 'Santa Fe', 'name_e' => 'Santa Fe', 'brand_id' => 10],
            ['name' => 'Tucson', 'name_e' => 'Tucson', 'brand_id' => 10],
            ['name' => 'Kona', 'name_e' => 'Kona', 'brand_id' => 10],

            ['name' => 'Optima', 'name_e' => 'Optima', 'brand_id' => 11],
            ['name' => ' Sorento', 'name_e' => ' Sorento', 'brand_id' => 11],
            ['name' => 'Sportage', 'name_e' => 'Sportage', 'brand_id' => 11],
            ['name' => 'Telluride', 'name_e' => 'Telluride', 'brand_id' => 11],
            ['name' => 'Soul', 'name_e' => 'Soul', 'brand_id' => 11],

            ['name' => 'Model S', 'name_e' => 'Model S', 'brand_id' => 12],
            ['name' => ' Model 3', 'name_e' => ' Model 3', 'brand_id' => 12],
            ['name' => 'Model X', 'name_e' => 'Model X', 'brand_id' => 12],
            ['name' => 'Model Y', 'name_e' => 'Model Y', 'brand_id' => 12],

            ['name' => 'Outback', 'name_e' => 'Outback', 'brand_id' => 13],
            ['name' => ' Forester', 'name_e' => ' Forester', 'brand_id' => 13],
            ['name' => 'Crosstrek', 'name_e' => 'Crosstrek', 'brand_id' => 13],
            ['name' => 'Impreza', 'name_e' => 'Impreza', 'brand_id' => 13],

            ['name' => 'Mazda3', 'name_e' => 'Mazda3', 'brand_id' => 14],
            ['name' => ' Mazda6', 'name_e' => ' Mazda6', 'brand_id' => 14],
            ['name' => 'CX-5', 'name_e' => 'CX-5', 'brand_id' => 14],
            ['name' => 'CX-9', 'name_e' => 'CX-9', 'brand_id' => 14],

            ['name' => 'ES', 'name_e' => 'ES', 'brand_id' => 15],
            ['name' => ' RX', 'name_e' => ' RX', 'brand_id' => 15],
            ['name' => 'IS', 'name_e' => 'IS', 'brand_id' => 15],
            ['name' => 'NX', 'name_e' => 'NX', 'brand_id' => 15],
            ['name' => 'GX', 'name_e' => 'GX', 'brand_id' => 15],

            ['name' => 'XC90', 'name_e' => 'XC90', 'brand_id' => 16],
            ['name' => 'XC60', 'name_e' => 'XC60', 'brand_id' => 16],
            ['name' => 'S60', 'name_e' => 'S60', 'brand_id' => 16],
            ['name' => 'V60', 'name_e' => 'V60', 'brand_id' => 16],
            ['name' => 'V90', 'name_e' => 'V90', 'brand_id' => 16],

            ['name' => '911', 'name_e' => '911', 'brand_id' => 17],
            ['name' => 'Cayenne', 'name_e' => 'Cayenne', 'brand_id' => 17],
            ['name' => 'Panamera', 'name_e' => 'Panamera', 'brand_id' => 17],
            ['name' => 'Macan', 'name_e' => 'Macan', 'brand_id' => 17],

            ['name' => 'Range Rover', 'name_e' => 'Range Rover', 'brand_id' => 18],
            ['name' => 'Discovery', 'name_e' => 'Discovery', 'brand_id' => 18],
            ['name' => 'Defender', 'name_e' => 'Defender', 'brand_id' => 18],
            ['name' => 'Evoque', 'name_e' => 'Evoque', 'brand_id' => 18],

            ['name' => '500', 'name_e' => '500', 'brand_id' => 19],
            ['name' => 'Panda', 'name_e' => 'Panda', 'brand_id' => 19],
            ['name' => 'Tipo', 'name_e' => 'Tipo', 'brand_id' => 19],
            ['name' => '500X', 'name_e' => '500X', 'brand_id' => 19],
            ['name' => '500L', 'name_e' => '500L', 'brand_id' => 19],

            ['name' => 'Outlander', 'name_e' => 'Outlander', 'brand_id' => 20],
            ['name' => 'Eclipse', 'name_e' => 'Eclipse', 'brand_id' => 20],
            ['name' => 'Cross', 'name_e' => 'Cross', 'brand_id' => 20],
            ['name' => 'Mirage', 'name_e' => 'Mirage', 'brand_id' => 20],
            ['name' => 'Pajero', 'name_e' => 'Pajero', 'brand_id' => 20],

            ['name' => 'Wrangler', 'name_e' => 'Wrangler', 'brand_id' => 21],
            ['name' => 'Grand Cherokee', 'name_e' => 'Grand Cherokee', 'brand_id' => 21],
            ['name' => 'Cherokee', 'name_e' => 'Cherokee', 'brand_id' => 21],
            ['name' => 'Renegade', 'name_e' => 'Renegade', 'brand_id' => 21],

            ['name' => 'Encore', 'name_e' => 'Encore', 'brand_id' => 22],
            ['name' => 'Enclave', 'name_e' => 'Enclave', 'brand_id' => 22],
            ['name' => 'Regal', 'name_e' => 'Regal', 'brand_id' => 22],
            ['name' => 'LaCrosse', 'name_e' => 'LaCrosse', 'brand_id' => 22],

            ['name' => 'Escalade', 'name_e' => 'Escalade', 'brand_id' => 23],
            ['name' => 'XT5', 'name_e' => 'XT5', 'brand_id' => 23],
            ['name' => 'CT4', 'name_e' => 'CT4', 'brand_id' => 23],
            ['name' => 'CT5', 'name_e' => 'CT5', 'brand_id' => 23],

            ['name' => 'Pacifica', 'name_e' => 'Pacifica', 'brand_id' => 24],
            ['name' => '300', 'name_e' => '300', 'brand_id' => 24],

            ['name' => 'Charger', 'name_e' => 'Charger', 'brand_id' => 25],
            ['name' => 'Challenger', 'name_e' => 'Challenger', 'brand_id' => 25],
            ['name' => 'Durango', 'name_e' => 'Durango', 'brand_id' => 25],
            ['name' => 'Journey', 'name_e' => 'Journey', 'brand_id' => 25],

            ['name' => 'F-Pace', 'name_e' => 'F-Pace', 'brand_id' => 26],
            ['name' => 'XE', 'name_e' => 'XE', 'brand_id' => 26],
            ['name' => 'XF', 'name_e' => 'XF', 'brand_id' => 26],
            ['name' => 'E-Pace', 'name_e' => 'E-Pace', 'brand_id' => 26],
            ['name' => 'I-Pace', 'name_e' => 'I-Pace', 'brand_id' => 26],

            ['name' => '1500', 'name_e' => '1500', 'brand_id' => 27],
            ['name' => '2500', 'name_e' => '2500', 'brand_id' => 27],
            ['name' => '3500', 'name_e' => '3500', 'brand_id' => 27],

            ['name' => 'G70', 'name_e' => 'G70', 'brand_id' => 28],
            ['name' => 'G80', 'name_e' => 'G80', 'brand_id' => 28],
            ['name' => 'G90', 'name_e' => 'G90', 'brand_id' => 28],

            ['name' => 'Giulia', 'name_e' => 'Giulia', 'brand_id' => 29],
            ['name' => 'Stelvio', 'name_e' => 'Stelvio', 'brand_id' => 29],

            ['name' => 'Cooper', 'name_e' => 'Cooper', 'brand_id' => 30],
            ['name' => 'Countryman', 'name_e' => 'Countryman', 'brand_id' => 30],
            ['name' => 'Clubman', 'name_e' => 'Clubman', 'brand_id' => 30],

            ['name' => 'Ghibli', 'name_e' => 'Ghibli', 'brand_id' => 31],
            ['name' => 'Quattroporte', 'name_e' => 'Quattroporte', 'brand_id' => 31],
            ['name' => 'Levante', 'name_e' => 'Levante', 'brand_id' => 31],

            ['name' => 'MDX', 'name_e' => 'MDX', 'brand_id' => 32],
            ['name' => 'RDX', 'name_e' => 'RDX', 'brand_id' => 32],
            ['name' => 'TLX', 'name_e' => 'TLX', 'brand_id' => 32],

            ['name' => 'Q50', 'name_e' => 'Q50', 'brand_id' => 33],
            ['name' => 'QX60', 'name_e' => 'QX60', 'brand_id' => 33],
            ['name' => 'QX80', 'name_e' => 'QX80', 'brand_id' => 33],

            ['name' => 'C3', 'name_e' => 'C3', 'brand_id' => 34],
            ['name' => 'C4', 'name_e' => 'C4', 'brand_id' => 34],
            ['name' => 'C5', 'name_e' => 'C5', 'brand_id' => 34],
            ['name' => 'Berlingo', 'name_e' => 'Berlingo', 'brand_id' => 34],
            ['name' => 'C3 Aircross', 'name_e' => 'C3 Aircross', 'brand_id' => 34],

            ['name' => 'C3 Aircross', 'name_e' => 'C3 Aircross', 'brand_id' => 35],
            ['name' => '3008', 'name_e' => '3008', 'brand_id' => 35],
            ['name' => '5008', 'name_e' => '5008', 'brand_id' => 35],
            ['name' => '308', 'name_e' => '308', 'brand_id' => 35],
            ['name' => '2008', 'name_e' => '2008', 'brand_id' => 35],

            ['name' => 'Clio', 'name_e' => 'Clio', 'brand_id' => 36],
            ['name' => 'Megane', 'name_e' => 'Megane', 'brand_id' => 36],
            ['name' => 'Captur', 'name_e' => 'Captur', 'brand_id' => 36],
            ['name' => 'Kadjar', 'name_e' => 'Kadjar', 'brand_id' => 36],
            ['name' => 'Koleos', 'name_e' => 'Koleos', 'brand_id' => 36],

            ['name' => 'Octavia', 'name_e' => 'Octavia', 'brand_id' => 37],
            ['name' => 'Superb', 'name_e' => 'Superb', 'brand_id' => 37],
            ['name' => 'Kodiaq', 'name_e' => 'Kodiaq', 'brand_id' => 37],
            ['name' => 'Karoq', 'name_e' => 'Karoq', 'brand_id' => 37],
            ['name' => 'Scala', 'name_e' => 'Scala', 'brand_id' => 37],

            ['name' => 'Ibiza', 'name_e' => 'Ibiza', 'brand_id' => 38],
            ['name' => 'Leon', 'name_e' => 'Leon', 'brand_id' => 38],
            ['name' => 'Ateca', 'name_e' => 'Ateca', 'brand_id' => 38],
            ['name' => 'Tarraco', 'name_e' => 'Tarraco', 'brand_id' => 38],
            ['name' => 'Arona', 'name_e' => 'Arona', 'brand_id' => 38],

            ['name' => '488', 'name_e' => '488', 'brand_id' => 39],
            ['name' => 'Portofino', 'name_e' => 'Portofino', 'brand_id' => 39],
            ['name' => 'Roma', 'name_e' => 'Roma', 'brand_id' => 39],
            ['name' => 'SF90 Stradal', 'name_e' => 'SF90 Stradal', 'brand_id' => 39],
            ['name' => 'F8 Tributo', 'name_e' => 'F8 Tributo', 'brand_id' => 39],

            ['name' => 'Huracán', 'name_e' => 'Huracán', 'brand_id' => 40],
            ['name' => 'Aventador', 'name_e' => 'Aventador', 'brand_id' => 40],
            ['name' => 'Urus', 'name_e' => 'Urus', 'brand_id' => 40],

            ['name' => '720S', 'name_e' => '720S', 'brand_id' => 41],
            ['name' => 'GT', 'name_e' => 'GT', 'brand_id' => 41],
            ['name' => '570S', 'name_e' => '570S', 'brand_id' => 41],
            ['name' => '765LT', 'name_e' => '765LT', 'brand_id' => 41],

            ['name' => 'Chiron', 'name_e' => 'Chiron', 'brand_id' => 42],
            ['name' => 'Veyron', 'name_e' => 'Veyron', 'brand_id' => 42],
            ['name' => 'Divo', 'name_e' => 'Divo', 'brand_id' => 42],

            ['name' => 'Evora', 'name_e' => 'Evora', 'brand_id' => 43],
            ['name' => 'Elise', 'name_e' => 'Elise', 'brand_id' => 43],
            ['name' => 'Exige', 'name_e' => 'Exige', 'brand_id' => 43],

            ['name' => 'EQ Fortwo', 'name_e' => 'EQ Fortwo', 'brand_id' => 44],
            ['name' => ' EQ Forfour', 'name_e' => ' EQ Forfour', 'brand_id' => 44],

            ['name' => 'A110', 'name_e' => 'A110', 'brand_id' => 45],

            ['name' => 'Sandero', 'name_e' => 'Sandero', 'brand_id' => 46],
            ['name' => 'Duster', 'name_e' => 'Duster', 'brand_id' => 46],
            ['name' => 'Logan', 'name_e' => 'Logan', 'brand_id' => 46],

            ['name' => 'Swift', 'name_e' => 'Swift', 'brand_id' => 47],
            ['name' => 'Vitara', 'name_e' => 'Vitara', 'brand_id' => 47],
            ['name' => 'Jimny', 'name_e' => 'Jimny', 'brand_id' => 47],
            ['name' => 'SX4', 'name_e' => 'SX4', 'brand_id' => 47],
            ['name' => ' EQ FoBaleno', 'name_e' => ' EQ FoBaleno', 'brand_id' => 47],

            ['name' => 'Thar', 'name_e' => 'Thar', 'brand_id' => 48],
            ['name' => 'XUV300', 'name_e' => 'XUV300', 'brand_id' => 48],
            ['name' => 'Scorpio', 'name_e' => 'Scorpio', 'brand_id' => 48],
            ['name' => 'Bolero', 'name_e' => 'Bolero', 'brand_id' => 48],
            ['name' => 'XUV500', 'name_e' => 'XUV500', 'brand_id' => 48],

            ['name' => 'Nexon', 'name_e' => 'Nexon', 'brand_id' => 49],
            ['name' => 'Harrier', 'name_e' => 'Harrier', 'brand_id' => 49],
            ['name' => 'Altroz', 'name_e' => 'Altroz', 'brand_id' => 49],
            ['name' => 'Safari', 'name_e' => 'Safari', 'brand_id' => 49],
            ['name' => 'Tiago', 'name_e' => 'Tiago', 'brand_id' => 49],

            ['name' => 'Emgrand', 'name_e' => 'Emgrand', 'brand_id' => 50],
            ['name' => 'Coolray', 'name_e' => 'Coolray', 'brand_id' => 50],
            ['name' => 'Boyue', 'name_e' => 'Boyue', 'brand_id' => 50],
            ['name' => 'BinRui', 'name_e' => 'BinRui', 'brand_id' => 50],

            ['name' => 'Tiggo', 'name_e' => 'Tiggo', 'brand_id' => 51],
            ['name' => 'Arrizo', 'name_e' => 'Arrizo', 'brand_id' => 51],
            ['name' => 'QQ', 'name_e' => 'QQ', 'brand_id' => 51],

            ['name' => 'Tang', 'name_e' => 'Tang', 'brand_id' => 52],
            ['name' => 'Song', 'name_e' => 'Song', 'brand_id' => 52],
            ['name' => 'Yuan', 'name_e' => 'Yuan', 'brand_id' => 52],

            ['name' => 'Haval H6', 'name_e' => 'Haval H6', 'brand_id' => 53],
            ['name' => 'Haval Jolion', 'name_e' => 'Haval Jolion', 'brand_id' => 53],
            ['name' => 'Great Wall Cannon', 'name_e' => 'Great Wall Cannon', 'brand_id' => 53],
            ['name' => 'Great Wall P Series', 'name_e' => 'Great Wall P Series', 'brand_id' => 53],

            ['name' => 'X50', 'name_e' => 'X50', 'brand_id' => 54],
            ['name' => 'X70', 'name_e' => 'X70', 'brand_id' => 54],
            ['name' => 'Saga', 'name_e' => 'Saga', 'brand_id' => 54],
            ['name' => 'Persona', 'name_e' => 'Persona', 'brand_id' => 54],

            ['name' => 'Myvi', 'name_e' => 'Myvi', 'brand_id' => 55],
            ['name' => 'Axia', 'name_e' => 'Axia', 'brand_id' => 55],
            ['name' => 'Bezza', 'name_e' => 'Bezza', 'brand_id' => 55],
            ['name' => 'Alza', 'name_e' => 'Alza', 'brand_id' => 55],

            ['name' => 'CS75', 'name_e' => 'CS75', 'brand_id' => 56],
            ['name' => 'CS35', 'name_e' => 'CS35', 'brand_id' => 56],
            ['name' => 'Plus', 'name_e' => 'Plus', 'brand_id' => 56],
            ['name' => 'Eado', 'name_e' => 'Eado', 'brand_id' => 56],
            ['name' => 'Oshan X7', 'name_e' => 'Oshan X7', 'brand_id' => 56],

            ['name' => 'Rexton', 'name_e' => 'Rexton', 'brand_id' => 57],
            ['name' => 'Tivoli', 'name_e' => 'Tivoli', 'brand_id' => 57],
            ['name' => 'Korando', 'name_e' => 'Korando', 'brand_id' => 57],

            ['name' => 'V3', 'name_e' => 'V3', 'brand_id' => 58],
            ['name' => 'V5', 'name_e' => 'V5', 'brand_id' => 58],
            ['name' => 'H230', 'name_e' => 'H230', 'brand_id' => 58],

            ['name' => 'Aeolus', 'name_e' => 'Aeolus', 'brand_id' => 59],
            ['name' => 'Joyear', 'name_e' => 'Joyear', 'brand_id' => 59],
            ['name' => 'Fengon', 'name_e' => 'Fengon', 'brand_id' => 59],

            ['name' => 'D-Max', 'name_e' => 'D-Max', 'brand_id' => 60],
            ['name' => 'MU-X', 'name_e' => 'MU-X', 'brand_id' => 60],

            ['name' => 'View Traveller', 'name_e' => 'View Traveller', 'brand_id' => 61],
            ['name' => 'Toplander', 'name_e' => 'Toplander', 'brand_id' => 61],
            ['name' => 'Gratour', 'name_e' => 'Gratour', 'brand_id' => 61],
            ['name' => 'Thunder', 'name_e' => 'Thunder', 'brand_id' => 61],

            ['name' => 'JAC s3', 'name_e' => 'JAC s3', 'brand_id' => 62],
            ['name' => 'JAC S2', 'name_e' => 'JAC S2', 'brand_id' => 62],
            ['name' => 'JAC S7', 'name_e' => 'JAC S7', 'brand_id' => 62],
            ['name' => 'JAC T8', 'name_e' => 'JAC T8', 'brand_id' => 62],

            ['name' => 'GS4', 'name_e' => 'GS4', 'brand_id' => 63],
            ['name' => 'GS3', 'name_e' => 'GS3', 'brand_id' => 63],
            ['name' => 'GS8', 'name_e' => 'GS8', 'brand_id' => 63],
            ['name' => 'GA4', 'name_e' => 'GA4', 'brand_id' => 63],
            ['name' => 'GN6', 'name_e' => 'GN6', 'brand_id' => 63],

            ['name' => 'Haima 7X', 'name_e' => 'Haima 7X', 'brand_id' => 64],
            ['name' => 'Haima 8S', 'name_e' => 'Haima 8S', 'brand_id' => 64],
            ['name' => 'Haima S5', 'name_e' => 'Haima S5', 'brand_id' => 64],
            ['name' => 'Haima M6', 'name_e' => 'Haima M6', 'brand_id' => 64],

            ['name' => 'MG Hector', 'name_e' => 'MG Hector', 'brand_id' => 65],
            ['name' => 'MG ZS', 'name_e' => 'MG ZS', 'brand_id' => 65],
            ['name' => 'MG5', 'name_e' => 'MG5', 'brand_id' => 65],
            ['name' => 'MG RX5', 'name_e' => 'MG RX5', 'brand_id' => 65],

            ['name' => 'Vesta', 'name_e' => 'Vesta', 'brand_id' => 66],
            ['name' => 'Granta', 'name_e' => 'Granta', 'brand_id' => 66],
            ['name' => 'Niva', 'name_e' => 'Niva', 'brand_id' => 66],
            ['name' => 'XRAY', 'name_e' => 'XRAY', 'brand_id' => 66],

            ['name' => 'Phoenix', 'name_e' => 'Phoenix', 'brand_id' => 67],
            ['name' => 'TerrNo1', 'name_e' => 'TerrNo1', 'brand_id' => 67],
            ['name' => 'Jamal', 'name_e' => 'Jamal', 'brand_id' => 67],
            ['name' => 'T163', 'name_e' => 'T163', 'brand_id' => 67],

            ['name' => 'T700', 'name_e' => 'T700', 'brand_id' => 68],
            ['name' => 'T600', 'name_e' => 'T600', 'brand_id' => 68],
            ['name' => 'Z700', 'name_e' => 'Z700', 'brand_id' => 68],
            ['name' => 'SR9', 'name_e' => 'SR9', 'brand_id' => 68],

            ['name' => 'Hong Guang', 'name_e' => 'Hong Guang', 'brand_id' => 69],
            ['name' => 'Sunshine', 'name_e' => 'Sunshine', 'brand_id' => 69],
            ['name' => 'Rong Guang', 'name_e' => 'Rong Guang', 'brand_id' => 69],

            ['name' => 'X50', 'name_e' => 'X50', 'brand_id' => 70],
            ['name' => '520', 'name_e' => '520', 'brand_id' => 70],
            ['name' => '720', 'name_e' => '720', 'brand_id' => 70],
            ['name' => 'X60', 'name_e' => 'X60', 'brand_id' => 70],

            ['name' => 'H6 Coupe', 'name_e' => 'H6 Coupe', 'brand_id' => 71],
            ['name' => 'H9', 'name_e' => 'H9', 'brand_id' => 71],
            ['name' => 'H2', 'name_e' => 'H2', 'brand_id' => 71],
            ['name' => 'F7', 'name_e' => 'F7', 'brand_id' => 71],

            ['name' => 'Senova D50', 'name_e' => 'Senova D50', 'brand_id' => 72],
            ['name' => 'X25', 'name_e' => 'X25', 'brand_id' => 72],
            ['name' => 'EU5', 'name_e' => 'EU5', 'brand_id' => 72],
            ['name' => 'Weiwang M50F', 'name_e' => 'Weiwang M50F', 'brand_id' => 72],

            ['name' => 'Besturn X40', 'name_e' => 'Besturn X40', 'brand_id' => 73],
            ['name' => 'Besturn B30', 'name_e' => 'Besturn B30', 'brand_id' => 73],
            ['name' => 'Junpai D60', 'name_e' => 'Junpai D60', 'brand_id' => 73],

            ['name' => 'Daily', 'name_e' => 'Daily', 'brand_id' => 74],
            ['name' => 'Eurocargo', 'name_e' => 'Eurocargo', 'brand_id' => 74],
            ['name' => 'Stralis', 'name_e' => 'Stralis', 'brand_id' => 74],
            ['name' => 'Trakker', 'name_e' => 'Trakker', 'brand_id' => 74],

            ['name' => 'TGX', 'name_e' => 'TGX', 'brand_id' => 75],
            ['name' => 'TGS', 'name_e' => 'TGS', 'brand_id' => 75],
            ['name' => 'TGM', 'name_e' => 'TGM', 'brand_id' => 75],
            ['name' => 'TGL', 'name_e' => 'TGL', 'brand_id' => 75],

            ['name' => 'R-series', 'name_e' => 'R-series', 'brand_id' => 76],
            ['name' => 'S-series', 'name_e' => 'S-series', 'brand_id' => 76],
            ['name' => 'G-series', 'name_e' => 'G-series', 'brand_id' => 76],
            ['name' => 'P-series', 'name_e' => 'P-series', 'brand_id' => 76],

            ['name' => 'Sandero Stepway', 'name_e' => 'Sandero Stepway', 'brand_id' => 77],
            ['name' => 'Logan MCV', 'name_e' => 'Logan MCV', 'brand_id' => 77],
            ['name' => 'Dokker Van', 'name_e' => 'Dokker Van', 'brand_id' => 77],

            ['name' => 'eKUV100', 'name_e' => 'eKUV100', 'brand_id' => 78],
            ['name' => 'eXUV300', 'name_e' => 'eXUV300', 'brand_id' => 78],

            ['name' => 'ES8', 'name_e' => 'ES8', 'brand_id' => 79],
            ['name' => 'ES8', 'name_e' => 'ES8', 'brand_id' => 79],
            ['name' => 'EC6', 'name_e' => 'EC6', 'brand_id' => 79],

            ['name' => 'Lucid Air', 'name_e' => 'Lucid Air', 'brand_id' => 80],

            ['name' => 'R1T', 'name_e' => 'R1T', 'brand_id' => 81],
            ['name' => 'R1S', 'name_e' => 'R1S', 'brand_id' => 81],

            ['name' => 'Polestar 1', 'name_e' => 'Polestar 1', 'brand_id' => 82],
            ['name' => 'Polestar 2', 'name_e' => 'Polestar 2', 'brand_id' => 82],

            ['name' => 'Rimac C_Two', 'name_e' => 'Rimac C_Two', 'brand_id' => 83],

            ['name' => 'B 1', 'name_e' => 'B 1', 'brand_id' => 84],
            ['name' => 'B 2', 'name_e' => 'B 2', 'brand_id' => 84],

            ['name' => 'R1T', 'name_e' => 'R1T', 'brand_id' => 85],
            ['name' => 'R1S', 'name_e' => 'R1S', 'brand_id' => 85],

            ['name' => 'Polestar 1', 'name_e' => 'Polestar 1', 'brand_id' => 86],
            ['name' => 'Polestar 2', 'name_e' => 'Polestar 2', 'brand_id' => 86],

            ['name' => 'FF91', 'name_e' => 'FF91', 'brand_id' => 87],

            ['name' => 'B1', 'name_e' => 'B1', 'brand_id' => 88],
            ['name' => 'B2', 'name_e' => 'B2', 'brand_id' => 88],

            ['name' => 'Revero GT', 'name_e' => 'Revero GT', 'brand_id' => 89],

            ['name' => 'Rimac C_Two', 'name_e' => 'Rimac C_Two', 'brand_id' => 90],

            ['name' => 'Lucid Air', 'name_e' => 'Lucid Air', 'brand_id' => 91],

            ['name' => 'M-Byte', 'name_e' => 'M-Byte', 'brand_id' => 92],
            ['name' => 'K-Byte', 'name_e' => 'k-Byte', 'brand_id' => 92],

            ['name' => 'Alpha S', 'name_e' => 'Alpha S', 'brand_id' => 93],
            ['name' => 'Alpha T', 'name_e' => 'Alpha T', 'brand_id' => 93],
            ['name' => 'ECF Concept', 'name_e' => 'ECF Concept', 'brand_id' => 93],

            ['name' => 'Mono', 'name_e' => 'Mono', 'brand_id' => 94],
            ['name' => 'Mono R', 'name_e' => 'Mono R', 'brand_id' => 94],

            ['name' => 'Jesko', 'name_e' => 'Jesko', 'brand_id' => 95],
            ['name' => 'Gemera', 'name_e' => 'Gemera', 'brand_id' => 95],
            ['name' => 'Regera', 'name_e' => 'Regera', 'brand_id' => 95],

            ['name' => 'Griffith', 'name_e' => 'Griffith', 'brand_id' => 96],

            ['name' => 'C8 Preliator', 'name_e' => 'C8 Preliator', 'brand_id' => 97],

            ['name' => 'Huayra', 'name_e' => 'Huayra', 'brand_id' => 98],
            ['name' => ' Huayra Roadster', 'name_e' => ' Huayra Roadster', 'brand_id' => 98],
            ['name' => 'Zonda', 'name_e' => 'Zonda', 'brand_id' => 98],

            ['name' => 'D8 GTO', 'name_e' => 'D8 GTO', 'brand_id' => 99],
            ['name' => 'D8 GTO-JD70', 'name_e' => 'D8 GTO-JD70', 'brand_id' => 99],

            ['name' => 'IE (Intensa Emozione)', 'name_e' => 'IE (Intensa Emozione)', 'brand_id' => 100],

            ['name' => 'TSR-S', 'name_e' => 'TSR-S', 'brand_id' => 101],
            ['name' => ' TS1 GT', 'name_e' => ' TS1 GT', 'brand_id' => 101],

            ['name' => 'MF5', 'name_e' => 'MF5', 'brand_id' => 102],
            ['name' => 'MF4', 'name_e' => 'MF4', 'brand_id' => 102],

            ['name' => 'Hussarya', 'name_e' => 'Hussarya', 'brand_id' => 103],

            ['name' => ' Seven 270', 'name_e' => ' Seven 270', 'brand_id' => 104],
            ['name' => 'Seven 310', 'name_e' => 'Seven 310', 'brand_id' => 104],
            ['name' => 'Seven 360', 'name_e' => 'Seven 360', 'brand_id' => 104],

            ['name' => 'G40', 'name_e' => 'G40', 'brand_id' => 105],
            ['name' => 'G55', 'name_e' => 'G55', 'brand_id' => 105],
            ['name' => 'Akula', 'name_e' => 'Akula', 'brand_id' => 105],

            ['name' => 'Avezzano', 'name_e' => 'Avezzano', 'brand_id' => 106],
            ['name' => 'Esperante', 'name_e' => 'Esperante', 'brand_id' => 106],
            ['name' => 'GTR-1', 'name_e' => 'GTR-1', 'brand_id' => 106],

            ['name' => '05', 'name_e' => '05', 'brand_id' => 107],
            ['name' => '05RR', 'name_e' => '05RR', 'brand_id' => 107],

            ['name' => 'RapX', 'name_e' => 'RapX', 'brand_id' => 108],
            ['name' => 'Sniper', 'name_e' => 'Sniper', 'brand_id' => 108],

            ['name' => 'F7', 'name_e' => 'F7', 'brand_id' => 109],
            ['name' => 'F7R', 'name_e' => 'F7R', 'brand_id' => 109],

            ['name' => 'M500', 'name_e' => 'M500', 'brand_id' => 110],
            ['name' => 'M600', 'name_e' => 'M600', 'brand_id' => 110],

            ['name' => 'Roadyacht GTS', 'name_e' => 'Roadyacht GTS', 'brand_id' => 111],

            ['name' => 'R1', 'name_e' => 'R1', 'brand_id' => 112],
            ['name' => 'R2', 'name_e' => 'R2', 'brand_id' => 112],

            ['name' => 'Bullet', 'name_e' => 'Bullet', 'brand_id' => 113],
            ['name' => 'Fighter', 'name_e' => 'Fighter', 'brand_id' => 113],
            ['name' => 'Blenheim', 'name_e' => 'Blenheim', 'brand_id' => 113],

            ['name' => 'Spano', 'name_e' => 'Spano', 'brand_id' => 114],

            ['name' => 'RR99', 'name_e' => 'RR99', 'brand_id' => 115],
            ['name' => 'Gigi 311 GT', 'name_e' => 'Gigi 311 GT', 'brand_id' => 115],

            ['name' => 'RS 1000', 'name_e' => 'RS 1000', 'brand_id' => 116],
            ['name' => 'RS 2000', 'name_e' => 'RS 2000', 'brand_id' => 116],

            ['name' => 'SandRacer 500GT', 'name_e' => 'SandRacer 500GT', 'brand_id' => 117],

            ['name' => 'H2R', 'name_e' => 'H2R', 'brand_id' => 118],
            ['name' => 'H2 Speed', 'name_e' => 'H2 Speed', 'brand_id' => 118],

            ['name' => 'JS50', 'name_e' => 'JS50', 'brand_id' => 119],
            ['name' => 'JSRC', 'name_e' => 'JSRC', 'brand_id' => 119],

            ['name' => 'Atom', 'name_e' => 'Atom', 'brand_id' => 120],
            ['name' => 'Nomad', 'name_e' => 'Nomad', 'brand_id' => 120],
            ['name' => 'Ace', 'name_e' => 'Ace', 'brand_id' => 120],

            ['name' => 'Plus Four', 'name_e' => 'Plus Four', 'brand_id' => 121],
            ['name' => 'Plus Six', 'name_e' => 'Plus Six', 'brand_id' => 121],
            ['name' => '3 Wheeler', 'name_e' => '3 Wheeler', 'brand_id' => 121],

            ['name' => '21C', 'name_e' => '21C', 'brand_id' => 122],

            ['name' => 'D8 GTO', 'name_e' => 'D8 GTO', 'brand_id' => 123],
            ['name' => 'D8 GTO-JD70', 'name_e' => 'D8 GTO-JD70', 'brand_id' => 123],

            ['name' => 'X-Bow', 'name_e' => 'X-Bow', 'brand_id' => 124],
            ['name' => 'X-Bow', 'name_e' => 'X-Bow', 'brand_id' => 124],
            ['name' => 'X-Bow RR', 'name_e' => 'X-Bow RR', 'brand_id' => 124],

            ['name' => 'Hong Guang S3', 'name_e' => 'Hong Guang S3', 'brand_id' => 125],
            ['name' => 'Hong Guang S1', 'name_e' => 'Hong Guang S1', 'brand_id' => 125],

            ['name' => 'GTC', 'name_e' => 'GTC', 'brand_id' => 126],
            ['name' => 'Réacteur', 'name_e' => 'Réacteur', 'brand_id' => 126],
            ['name' => 'Christelle Coupe', 'name_e' => 'Christelle Coupe', 'brand_id' => 126],

        ];



        foreach ($carBrands as $brand) {
            CarModel::create([
                'name' => $brand['name'],
                'name_e' => $brand['name_e'],
                'brand_id' => $brand['brand_id'],
            ]);
        }
    }
}
