<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info("Comenzando insercciones...");

        $this->command->warn("Agregando el propietario de la empresa...");

        DB::table('owner')->insert([
            'name' => 'RoCuCode S.L.',
            'corportate_name' => 'RoCuCode S.L.',
            'CIF' => 'B12345678',
            'address' => 'Calle Mayor 45, 3ºB',
            'postal_code' => '29011',
            'city' => 'Málaga',
            'province' => 'Málaga',
            'country' => 'España',
            'phone' => '+34 951 123 456',
            'corporate_email' => 'info@tecnofix.es',
            'website' => 'https://www.tecnofix.es',
            'foundation_year' => 2014,
            'sector' => 'Reparación de dispositivos electrónicos',
            'short_description' => 'Empresa especializada en reparación exprés de móviles y tablets.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->command->info("Propietario: OK");

        $this->command->warn("Agregando usuario...");

        User::factory()->create([
            'name' => 'Filament Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345admin'),
            'active' => true,
        ]);
        User::factory()->create([
            'name' => 'DEVELOPER USER',
            'email' => 'developer@admin.com',
            'password' => bcrypt('12345developer'),
            'active' => true,
        ]);
        User::factory()->create([
            'name' => 'Salesman User',
            'email' => 'salesman@admin.com',
            'password' => bcrypt('12345salesman'),
            'active' => true,
        ]);
        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@admin.com',
            'password' => bcrypt('12345manager'),
            'active' => true,
        ]);
        User::factory()->create([
            'name' => 'Technician User',
            'email' => 'technician@admin.com',
            'password' => bcrypt('12345technician'),
            'active' => true,
        ]);
        
        $this->command->info("Usuario: OK");

        $this->command->warn("Agregando el marcas y modelos...");


        $path = database_path('seeders\data\import.csv');

        //SI NO EXISTE EL ARCHIVO. CONSOLA
        if (!File::exists($path)) {
            $this->command->error("Archivo CSV no encontrado: $path");
            return;
        }

        //
        $csv = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_shift($csv)); // ['brand_name', 'model']

        foreach ($csv as $row) {

            $data = array_combine($header, $row);

            if (!isset($data['brand'], $data['model'])) {
                continue;
            }

            //A MAYUSCULA
            $brandName = strtoupper(trim($data['brand']));

            //MODELO EN ARRAY 
            $modelName = strtoupper($data['model']);

            //vERIFICA SI EXISTE EL MODELO, SINO LO CREA
            $brand = DB::table('brands')->where('name', $brandName)->first();
            if (!$brand) {
                $brandId = DB::table('brands')->insertGetId([
                    'name' => $brandName,
                ]);
            } else {
                $brandId = $brand->id;
            }

            //HACEMOS LA MISMA VAINA QUE CON LAS MARCAS
            DB::table('device_models')->updateOrInsert(
                ['name' => $modelName, 'brand_id' => $brandId],
                ['name' => $modelName, 'brand_id' => $brandId]
            );
        }

        $this->command->info("Marcas:OK");
        $this->command->info("Modelos:OK");

        $this->command->warn("Agregando impuestos, métodos de pago, tipos de documentos, estados, roles, tipos de productos y categorías...");

        DB::table('taxes')->insert([
            ['name' => 'IVA', 'percentage' => 10],
            ['name' => 'IVA', 'percentage' => 21],
            ['name' => 'SIN IMPUESTOS', 'percentage' => 0],
        ]);
        $this->command->info("Impuestos: OK");

        DB::table('payment_methods')->insert([
            ['name' => 'TARJETA'],
            ['name' => 'EFECTIVO'],
            ['name' => 'TRANSFERENCIA'],
        ]);

        $this->command->info("Metodos de pago: OK");

        DB::table('document_types')->insert([
            ['name' => 'DNI'],
            ['name' => 'NIE'],
            ['name' => 'PASAPORTE'],
            ['name' => 'OTRO'],
        ]);

        $this->command->info("Tipo de Documento: OK");

        DB::table('statuses')->insert([
            ['name' => 'PENDIENTE'],
            ['name' => 'PENDIENTE DE PIEZA'],
            ['name' => 'COMPLETADO'],
            ['name' => 'FACTURADO'],
            ['name' => 'CANCELADO'],
            ['name' => 'EN REPARACIÓN'],
            ['name' => 'DEVOLUCIÓN COMPLETA'],
            ['name' => 'DEVOLUCIÓN PARCIAL'],
            ['name' => 'ENTREGADO'],
        ]);


        $this->command->info("Estados: OK");

        DB::table('roles')->insert([
            ['name' => 'ADMIN'],
            ['name' => 'DEPENDIENTE'],
            ['name' => 'TÉCNICO'],
            ['name' => 'ENCARGADO'],
            ['name' => 'DESARROLLADOR'],
        ]);

        $this->command->info("Roles: OK");

        DB::table('types')->insert([
            ['name' => 'PANTALLA'],
            ['name' => 'BATERÍA'],
            ['name' => 'CONECTOR DE CARGA'],
            ['name' => 'FLEX DE CARGA'],
            ['name' => 'FLEX'],
            ['name' => 'LENTE'],
            ['name' => 'TAPA'],
            ['name' => 'CÁMARA'],
            ['name' => 'REPARACIÓN DE PLACA'],
            ['name' => 'OTRO'],
        ]);

        $this->command->info("Tipos de productos: OK");

        DB::table('categories')->insert([
            ['name' => 'SERVICIOS', 'tax_id' => 1],
            ['name' => 'PIEZAS', 'tax_id' => 1],
            ['name' => 'ACCESORIOS', 'tax_id' => 1],
            ['name' => 'REACONDICIONADOS', 'tax_id' => 3],
            ['name' => 'PROPINAS', 'tax_id' => 3],
        ]);

        $this->command->info("Categorias: OK");


        $this->command->warn("Agregando clientes, empresas, tiendas y tiempos de reparación, dispositivos y perfiles...");

        DB::table('clients')->insert([
            [
                'document' => '12345678Z',
                'name' => 'JUAN',
                'surname' => 'PÉREZ',
                'surname2' => 'GARCÍA',
                'phone_number' => '600123456',
                'phone_number_2' => '600654321',
                'postal_code' => '28001',
                'address' => 'CALLE MAYOR, 1',
                'document_type_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'document' => 'X1234567S',
                'name' => 'MARÍA',
                'surname' => 'LÓPEZ',
                'surname2' => null,
                'phone_number' => '610987654',
                'phone_number_2' => null,
                'postal_code' => '28002',
                'address' => 'CALLE GRAN VÍA, 2',
                'document_type_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'document' => '87654321X',
                'name' => 'CARLOS',
                'surname' => 'MARTÍNEZ',
                'surname2' => 'FERNÁNDEZ',
                'phone_number' => '620123789',
                'phone_number_2' => '620987321',
                'postal_code' => '28003',
                'address' => 'CALLE ALCALÁ, 3',
                'document_type_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("Clientes: OK");

        DB::table('companies')->insert([
            [
                'cif' => 'A12345678',
                'name' => 'TECH SOLUTIONS',
                'corporate_name' => 'TECH SOLUTIONS S.L.',
                'address' => 'CALLE INNOVACIÓN, 10',
                'postal_code' => '28004',
                'locality' => 'MADRID',
                'province' => 'MADRID',
                'discount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cif' => 'B87654321',
                'name' => 'GREEN ENERGY',
                'corporate_name' => 'GREEN ENERGY S.A.',
                'address' => 'AVENIDA VERDE, 20',
                'postal_code' => '28005',
                'locality' => 'MADRID',
                'province' => 'MADRID',
                'discount' => 3.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cif' => 'C11223344',
                'name' => 'SMART DEVICES',
                'corporate_name' => 'SMART DEVICES CORP.',
                'address' => 'PASEO INTELIGENTE, 30',
                'postal_code' => '28006',
                'locality' => 'MADRID',
                'province' => 'MADRID',
                'discount' => 5.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cif' => 'D55667788',
                'name' => 'MANZANA ROTA',
                'corporate_name' => 'MANZANA ROTA S.L.',
                'address' => 'CALLE UNION, 83',
                'postal_code' => '28007',
                'locality' => 'MÁLAGA',
                'province' => 'MÁLAGA',
                'discount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("Empresas: OK");

        DB::table('stores')->insert([
            [
                'name' => 'PLAZA MAYOR',
                'address' => 'CALLE REPARACIÓN, 1',
                'prefix' => '+34',
                'number' => '666 666 664',
                'email' => 'plazamayor@tecnofix.es',
                'schedule' => 'Lunes a Viernes 10:00-20:00',
                'work_order_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'POLIGONO SANTA BARBARA',
                'address' => 'CALLE ENERGÍA, 2',
                'prefix' => '+34',
                'number' => '666 666 665',
                'email' => 'poligonosb@tecnofix.es',
                'schedule' => 'Lunes a Sábado 10:00-14:00 17:00-21:00',
                'work_order_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VIALIA',
                'address' => 'CALLE CARGA, 3',
                'prefix' => '+34',
                'number' => '666 666 666 ',
                'email' => 'vialia@tecnofix.es',
                'schedule' => 'Lunes a Sábado 10:00-22:00',
                'work_order_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("Tiendas: OK");

        DB::table('repair_times')->insert([
            [
                'name' => '1 HORA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2 HORAS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '3 HORAS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '1 DÍA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2 DÍAS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '3 - 5 DÍAS LABORALES',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '10 - 15 DÍAS LABORALES',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SE AVISARÁ CUANDO ENCONTREMOS LA AVERÍA.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SE AVISARÁ CUANDO LA REPARACION ESTÉ FINALIZADA.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("Tiempos de repación: OK");

        DB::table('devices')->insert([
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN1234567890',
                'IMEI' => '123456789012345',
                'colour' => 'Black',
                'unlock_code' => '1234',
                'device_model_id' => 1,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN0987654321',
                'IMEI' => '543210987654321',
                'colour' => 'White',
                'unlock_code' => '5678',
                'device_model_id' => 2,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Blue',
                'unlock_code' => null,
                'device_model_id' => 3,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN5555555555',
                'IMEI' => '555555555555555',
                'colour' => 'Red',
                'unlock_code' => '9999',
                'device_model_id' => 1,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN6666666666',
                'IMEI' => '666666666666666',
                'colour' => 'Green',
                'unlock_code' => '8888',
                'device_model_id' => 2,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("Dispositivos: OK");

        DB::table('store_user')->insert([
            [
                'user_id' => 1,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'store_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'store_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'store_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'store_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'store_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'store_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'store_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'store_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info("Usuarios agregados a tiendas: OK ");

        DB::table('rol_user')->insert([
            [
                'user_id' => 1,
                'rol_id' => 1, // ADMIN
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'rol_id' => 2, // DEPENDIENTE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'rol_id' => 3, // TÉCNICO
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'rol_id' => 4, // MANGER
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'rol_id' => 2, // DEPENDIENTE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'rol_id' => 4, // ENCARGADO
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'rol_id' => 3, // TÉCNICO
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'rol_id' => 5, // DESARROLLADOR
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'rol_id' => 2, // DEPENDIENTE
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'rol_id' => 3, // TÉCNICO
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'rol_id' => 4, // ENCARGADO
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'rol_id' => 1, // ADMIN
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info("Perfiles agregados a Usuario: OK ");

        $this->command->warn("Agregando items...");

        //PIEZAS
        $deviceModels = DB::table('device_models')->get();
        $parts = ['Pantalla', 'Batería', 'Conector de carga', 'Cámara', 'Reparación de placa'];
        $distributors = ['KF', 'PA', 'SS'];
        $typeId = DB::table('types')->where('name', 'PIEZA')->value('id');
        $categoryId = DB::table('categories')->where('name', 'PIEZAS')->value('id');

        foreach ($deviceModels as $deviceModel) {

            foreach ($parts as $part) {
                $brandName = DB::table('brands')
                    ->join('device_models', 'brands.id', '=', 'device_models.brand_id')
                    ->where('device_models.id', $deviceModel->id)
                    ->value('brands.name');
                $itemId = DB::table('items')->insertGetId([
                    'name' => "{$part} para {$brandName} {$deviceModel->name}",
                    'cost' => rand(10, 100), // Random cost
                    'price' => rand(150, 300), // Random price
                    'distributor' => $distributors[array_rand($distributors)],
                    'type_id' => match ($part) {
                        'Pantalla' => 1,
                        'Batería' => 2,
                        'Conector de carga' => 3,
                        'Cámara' => 6,
                        'Reparación de placa' => 9,
                        default => $typeId,
                    },
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('device_model_item')->insert([
                    'device_model_id' => $deviceModel->id,
                    'item_id' => $itemId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        //ACCESORIOS
        $accessories = [
            'FUNDA DE SILICONA',
            'PROTECTOR DE PANTALLA DE VIDRIO TEMPLADO',
            'CARGADOR RÁPIDO',
            'AURICULARES INALÁMBRICOS',
            'SOPORTE PARA COCHE',
            'CABLE USB-C',
            'CABLE LIGHTNING',
            'POWER BANK',
            'ALTAVOZ BLUETOOTH',
            'TECLADO INALÁMBRICO',
            'RATÓN INALÁMBRICO',
            'ADAPTADOR HDMI',
            'LÁPIZ TÁCTIL',
            'SOPORTE PARA TABLET',
            'CARGADOR INALÁMBRICO',
            'TARJETA DE MEMORIA',
            'LECTOR DE TARJETAS',
            'ADAPTADOR DE CORRIENTE UNIVERSAL',
            'FUNDA IMPERMEABLE',
            'SOPORTE PLEGABLE',
            'CARGADOR SOLAR',
            'SOPORTE PARA ESCRITORIO',
            'CARGADOR MÚLTIPLE',
            'ADAPTADOR DE AUDIO',
            'FUNDA CON TECLADO',
            'PROTECTOR DE CÁMARA',
            'SOPORTE MAGNÉTICO',
            'CARGADOR PARA COCHE',
            'FUNDA ANTICHOQUE',
            'CABLE RETRÁCTIL',
        ];

        $categoryId = DB::table('categories')->where('name', 'ACCESORIOS')->value('id');
        $typeId = DB::table('types')->where('name', 'ACCESORIO')->value('id');

        foreach ($accessories as $accessory) {
            DB::table('items')->insert([
                'name' => $accessory,
                'cost' => rand(5, 50), // Random cost
                'price' => rand(20, 100), // Random price
                'distributor' => $distributors[array_rand($distributors)],
                'type_id' => $typeId,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        //SERVICIOS
        $services = [
            'REPARACIÓN DE SOFTWARE',
            'DIAGNÓSTICO TÉCNICO',
            'LIMPIEZA INTERNA',
            'ACTUALIZACIÓN DE FIRMWARE',
            'RECUPERACIÓN DE DATOS',
            'INSTALACIÓN DE SISTEMA OPERATIVO',
            'CONFIGURACIÓN DE DISPOSITIVO',
            'ELIMINACIÓN DE VIRUS',
            'OPTIMIZACIÓN DE RENDIMIENTO',
            'SOPORTE TÉCNICO REMOTO',
        ];

        $categoryId = DB::table('categories')->where('name', 'SERVICIOS')->value('id');
        $typeId = DB::table('types')->where('name', 'OTRO')->value('id');

        foreach ($services as $service) {
            DB::table('items')->insert([
                'name' => $service,
                'cost' => rand(20, 100), // Random cost
                'price' => rand(50, 200), // Random price
                'distributor' => "ERPAIR", // No distributor for services
                'type_id' => $typeId,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $categoryId = DB::table('categories')->where('name', 'PROPINAS')->value('id');
        $typeId = DB::table('types')->where('name', 'OTRO')->value('id');

        //PROPINA
        DB::table('items')->insert([
            'name' => 'PROPINA',
            'cost' => 0, // No cost for tips
            'price' => 2, // Default price for tips
            'distributor' => "", // No distributor for tips
            'type_id' => $typeId,
            'category_id' => $categoryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reconditionedDevices = [
            'IPHONE 12 128GB NEGRO',
            'IPHONE 12 256GB BLANCO',
            'IPHONE 13 128GB AZUL',
            'IPHONE 13 256GB ROJO',
            'IPHONE 14 128GB VERDE',
            'IPHONE 14 256GB DORADO',
            'IPHONE 14 PRO 128GB MORADO',
            'IPHONE 14 PRO 256GB PLATA',
        ];

        $categoryId = DB::table('categories')->where('name', 'REACONDICIONADOS')->value('id');
        $typeId = DB::table('types')->where('name', 'OTRO')->value('id');

        foreach ($reconditionedDevices as $device) {
            DB::table('items')->insert([
                'name' => $device,
                'cost' => rand(200, 800), // Random cost
                'price' => rand(1000, 1500), // Random price
                'distributor' => $distributors[array_rand($distributors)],
                'type_id' => $typeId,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("Items: OK");

        $this->command->warn("Agregando items a todas las tiendas...");

        $items = DB::table('items')->get();
        $stores = DB::table('stores')->get();

        foreach ($stores as $store) {
            foreach ($items as $item) {
                DB::table('stocks')->insert([
                    'store_id' => $store->id,
                    'item_id' => $item->id,
                    'quantity' => rand(1, 5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info("Items agregados a todas las tiendas: OK");

        $this->command->warn("Agregando órdenes de trabajo, setteando status, agregrando items aleatorios relacionados...");
        DB::table('work_orders')->insert([
            [
                'work_order_number' => 1,
                'work_order_number_warranty' => null,
                'failure' => 'Pantalla rota, no enciende.',
                'private_comment' => 'Cliente muy exigente.',
                'comment' => 'Revisar antes de entregar.',
                'physical_condition' => 'Rayaduras leves en la carcasa.',
                'humidity' => 'Sin signos de humedad.',
                'test' => 'No pasa test de encendido.',
                'user_id' => 1,
                'device_id' => 1,
                'repair_time_id' => 1,
                'store_id' => 1,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            [
                'work_order_number' => 1,
                'work_order_number_warranty' => null,
                'failure' => 'Batería no carga.',
                'private_comment' => null,
                'comment' => 'Cliente solicita presupuesto.',
                'physical_condition' => 'Buen estado general.',
                'humidity' => 'No hay humedad.',
                'test' => 'No carga con ningún cargador.',
                'user_id' => 2,
                'device_id' => 2,
                'repair_time_id' => 2,
                'store_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_order_number' => 1,
                'work_order_number_warranty' => null,
                'failure' => 'No funciona el altavoz.',
                'private_comment' => 'Posible daño por agua.',
                'comment' => null,
                'physical_condition' => 'Oxidación en el conector.',
                'humidity' => 'Humedad detectada.',
                'test' => 'Altavoz no emite sonido.',
                'user_id' => 3,
                'device_id' => 3,
                'repair_time_id' => 3,
                'store_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_order_number' => 2,
                'work_order_number_warranty' => null,
                'failure' => 'Problemas con el botón de encendido.',
                'private_comment' => null,
                'comment' => 'Cliente necesita reparación urgente.',
                'physical_condition' => 'Botón flojo.',
                'humidity' => 'Sin humedad.',
                'test' => 'Botón no responde.',
                'user_id' => 4,
                'device_id' => 4,
                'repair_time_id' => 4,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_order_number' => 3,
                'work_order_number_warranty' => null,
                'failure' => 'Pantalla táctil no responde.',
                'private_comment' => 'Cliente habitual.',
                'comment' => null,
                'physical_condition' => 'Pantalla sin daños visibles.',
                'humidity' => 'No hay humedad.',
                'test' => 'No responde al tacto.',
                'user_id' => 1,
                'device_id' => 5,
                'repair_time_id' => 5,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_order_number' => 4,
                'work_order_number_warranty' => null,
                'failure' => 'Problema de placa base.',
                'private_comment' => null,
                'comment' => null,
                'physical_condition' => 'Estado OK.',
                'humidity' => 'No se aprecia humedad.',
                'test' => 'Terminal no hace nada. No Carga. No enciende.',
                'user_id' => 1,
                'device_id' => 2,
                'repair_time_id' => 5,
                'store_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $workOrders = DB::table('work_orders')->get();
        
        
        foreach ($workOrders as $workOrder) {
            if ($workOrder->id == 3) {

                DB::table('status_work_order')->insert([
                    'work_order_id' => $workOrder->id,
                    'user_id' => $workOrder->user_id,
                    'status_id' => 1,
                    'created_at' => now()->subHour(),
                    'updated_at' => now()->subHour(),
                ]);

                DB::table('status_work_order')->insert([
                    'work_order_id' => $workOrder->id,
                    'user_id' => $workOrder->user_id,
                    'status_id' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                continue;
            }
            DB::table('status_work_order')->insert([
                'work_order_id' => $workOrder->id,
                'user_id' => $workOrder->user_id,
                'status_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table('status_work_order')->insert([
            'work_order_id' => 1,
            'user_id' => 1,
            'status_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        foreach ($workOrders as $workOrder) {
            $device = DB::table('devices')->where('id', $workOrder->device_id)->first();
            if (!$device) {
                continue;
            }
            $deviceModelId = $device->device_model_id;
            $itemIds = DB::table('device_model_item')
                ->where('device_model_id', $deviceModelId)
                ->pluck('item_id')
                ->take(2);

            foreach ($itemIds as $itemId) {
                DB::table('item_work_order')->insert([
                    'work_order_id' => $workOrder->id,
                    'item_id' => $itemId,
                    'modified_amount' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }

}
