<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $viewsInfo = [
            1  => ['name' => 'Dashboard',                 'slug' => 'dashboard'],
            2  => ['name' => 'Procesos',                  'slug' => 'procesos'],
            3  => ['name' => 'Archivos TXT',              'slug' => 'archivos-txt'],
            4  => ['name' => 'Pagos Bancos',              'slug' => 'pagos-banco'],
            5  => ['name' => 'Postulantes',               'slug' => 'postulantes'],
            6  => ['name' => 'Distribución de Vacantes',  'slug' => 'distribucion-de-vacantes'],
            7  => ['name' => 'Enviar Correos',            'slug' => 'enviar-correos'],
            8  => ['name' => 'Válidos',                   'slug' => 'validos'],
            9  => ['name' => 'Observados',                'slug' => 'observados'],
            10 => ['name' => 'Reitero Observados',        'slug' => 'reitero-observados'],
            11 => ['name' => 'Rectificados',              'slug' => 'rectificados'],
            12 => ['name' => 'Maestros',                  'slug' => 'maestros'],
            13 => ['name' => 'Exámenes',                  'slug' => 'examenes'],
            14 => ['name' => 'Modalidades',               'slug' => 'modalidades'],
            15 => ['name' => 'Sedes',                     'slug' => 'sedes'],
            16 => ['name' => 'Facultades',                'slug' => 'facultades'],
            17 => ['name' => 'Grupos Académicos',         'slug' => 'grupos-academicos'],
            18 => ['name' => 'Programas Académicos',      'slug' => 'programas-academicos'],
            19 => ['name' => 'Universidades',             'slug' => 'universidades'],
            20 => ['name' => 'Colegios',                  'slug' => 'colegios'],
            21 => ['name' => 'Estados de Postulante',     'slug' => 'estados-de-postulante'],
            22 => ['name' => 'Tipos de Direcciones',      'slug' => 'tipos-de-direcciones'],
            23 => ['name' => 'Géneros',                   'slug' => 'generos'],
            24 => ['name' => 'Distritos',                 'slug' => 'distritos'],
            25 => ['name' => 'Países',                    'slug' => 'paises'],
            26 => ['name' => 'Configuración',             'slug' => 'configuracion'],
            27 => ['name' => 'Vistas',                    'slug' => 'vistas'],
            28 => ['name' => 'Roles',                     'slug' => 'roles'],
            29 => ['name' => 'Usuarios',                  'slug' => 'usuarios'],
        ];

        // Acciones permitidas por vista
        $viewPermissions = [
            1  => ['view'],                                      // Dashboard
            2  => ['view', 'create', 'update', 'delete'],       // Procesos
            3  => ['view', 'import'],                           // Archivos TXT
            4  => ['view', 'export'],                           // Pagos Bancos
            5  => ['view', 'export'],                           // Postulantes
            6  => ['view', 'update'],                           // Distribución de Vacantes
            7  => ['view'],                                      // Enviar Correos (padre)
            8  => ['view', 'send'],                             // Válidos
            9  => ['view', 'send'],                             // Observados
            10 => ['view', 'send'],                             // Reitero Observados
            11 => ['view', 'send'],                             // Rectificados
            12 => ['view'],                                      // Maestros (padre)
            13 => ['view', 'create', 'update', 'delete'],       // Exámenes
            14 => ['view', 'create', 'update', 'delete'],       // Modalidades
            15 => ['view', 'create', 'update', 'delete'],       // Sedes
            16 => ['view', 'create', 'update', 'delete'],       // Facultades
            17 => ['view', 'create', 'update', 'delete'],       // Grupos Académicos
            18 => ['view', 'create', 'update', 'delete'],       // Programas Académicos
            19 => ['view', 'create', 'update', 'delete'],       // Universidades
            20 => ['view', 'create', 'update', 'delete'],       // Colegios
            21 => ['view', 'create', 'update', 'delete'],       // Estados de Postulante
            22 => ['view', 'create', 'update', 'delete'],       // Tipos de Direcciones
            23 => ['view', 'create', 'update', 'delete'],       // Géneros
            24 => ['view', 'create', 'update', 'delete'],       // Distritos
            25 => ['view', 'create', 'update', 'delete'],       // Países
            26 => ['view'],                                      // Configuración (padre)
            27 => ['view', 'create', 'update', 'delete'],       // Vistas
            28 => ['view', 'create', 'update', 'delete'],       // Roles
            29 => ['view', 'create', 'update', 'delete'],       // Usuarios
        ];

        $actions = config('permissions.actions');

        foreach ($viewPermissions as $viewId => $allowedActions) {
            $view = $viewsInfo[$viewId];

            foreach ($allowedActions as $actionKey) {
                $action = $actions[$actionKey];
                $code = $view['slug'] . '.' . $actionKey;

                Permission::updateOrCreate(
                    ['code' => $code],
                    [
                        'view_id'       => $viewId,
                        'name'          => $action['label'] . ' ' . $view['name'],
                        'description'   => $action['description'],
                        'module'        => $view['slug'],
                        'policy_method' => $action['policy_method'],
                        'is_active'     => true,
                    ]
                );
            }
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
