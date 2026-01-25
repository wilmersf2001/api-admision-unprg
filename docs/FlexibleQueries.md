# FlexibleQueries - Documentacion Completa

Trait para Laravel que permite aplicar filtros, ordenamiento y paginacion dinamicos a traves de configuraciones declarativas en los modelos.

## Tabla de Contenidos

- [Instalacion](#instalacion)
- [Configuracion Basica](#configuracion-basica)
- [Tipos de Filtros](#tipos-de-filtros)
- [Ordenamiento](#ordenamiento)
- [Paginacion](#paginacion)
- [Modelos de Ejemplo](#modelos-de-ejemplo)
- [Uso en Controladores](#uso-en-controladores)
- [Ejemplos de URLs](#ejemplos-de-urls)

---

## Instalacion

El trait se encuentra en `app/Http/Traits/FlexibleQueries.php`. Para usarlo en un modelo:

```php
<?php

namespace App\Models;

use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Model;

class MiModelo extends Model
{
    use FlexibleQueries;

    // Configuracion requerida...
}
```

---

## Configuracion Basica

Cada modelo que use el trait debe implementar dos metodos:

### `getFilterConfig(): array`

Define que filtros estan disponibles y como funcionan.

### `getSortConfig(): array`

Define que columnas pueden ser usadas para ordenar.

```php
protected function getFilterConfig(): array
{
    return [
        // Configuracion de filtros...
    ];
}

protected function getSortConfig(): array
{
    return [
        'allowed' => ['id', 'name', 'created_at'],  // Columnas permitidas
        'default' => 'id',                           // Ordenamiento por defecto
    ];
}
```

---

## Tipos de Filtros

Basado en el codigo fuente, existen **5 tipos de filtros** mas el **tipo por defecto**:

### 1. Filtro por Defecto (sin type o type no reconocido)

Cuando no se especifica `type` o se usa un valor no reconocido, se aplica `applyDirectFilter`. Soporta operadores: `=`, `!=`, `>`, `<`, `>=`, `<=`, `LIKE`, `IN`.

```php
// Sin especificar type (usa operador = por defecto)
'estado' => [
    'column' => 'estado',
],

// Con operador LIKE
'nombre' => [
    'column' => 'nombre',
    'operator' => 'LIKE',
],

// Con operador IN para multiples valores
'estado' => [
    'column' => 'estado',
    'operator' => 'IN',
],
```

**URLs de ejemplo:**
```
GET /api/items?estado=1
GET /api/items?nombre=Juan
GET /api/items?nombre=Juan&nombre_operator=like
GET /api/items?estado=1,2,3  (cuando operator es IN)
```

### 2. Busqueda Global (`global_search`)

Busca en multiples columnas y/o relaciones con `LIKE`.

```php
'search' => [
    'type' => 'global_search',
    'columns' => ['nombre', 'email', 'descripcion'],  // Columnas locales
    'relations' => [                                   // Opcional: relaciones
        'facultad' => ['nombre', 'codigo'],
        'programa' => ['nombre'],
    ],
]
```

**URL de ejemplo:**
```
GET /api/items?search=ingenieria
GET /api/items?search=juan%20perez
```

### 3. Rango de Fechas (`date_range`)

Filtra por rango de fechas usando `_start` y `_end`.

```php
'fecha_inscripcion' => [
    'type' => 'date_range',
    'column' => 'fecha_inscripcion',
]
```

**URLs de ejemplo:**
```
GET /api/items?fecha_inscripcion_start=2026-01-01
GET /api/items?fecha_inscripcion_end=2026-12-31
GET /api/items?fecha_inscripcion_start=2026-01-01&fecha_inscripcion_end=2026-06-30
```

### 4. Rango Numerico (`number_range`)

Filtra por rango numerico usando `_min` y `_max`.

```php
'puntaje' => [
    'type' => 'number_range',
    'column' => 'puntaje',
]
```

**URLs de ejemplo:**
```
GET /api/items?puntaje_min=50
GET /api/items?puntaje_max=100
GET /api/items?puntaje_min=50&puntaje_max=100
```

### 5. Filtro de Relacion (`relation`)

Filtra basandose en columnas de modelos relacionados usando `whereHas`.

```php
'facultad_nombre' => [
    'type' => 'relation',
    'relation' => 'facultad',      // Nombre de la relacion en el modelo
    'column' => 'nombre',          // Columna en la tabla relacionada
    'operator' => 'LIKE',          // Operador a usar (opcional, default =)
]
```

**URL de ejemplo:**
```
GET /api/items?facultad_nombre=ingenieria
```

### 6. Filtro de Existencia (`exists`)

Filtra por existencia o no de una relacion usando `whereHas` / `whereDoesntHave`.

```php
'tiene_postulaciones' => [
    'type' => 'exists',
    'relation' => 'postulaciones',
]
```

**URLs de ejemplo:**
```
GET /api/items?tiene_postulaciones=true   // Solo items con postulaciones
GET /api/items?tiene_postulaciones=false  // Solo items sin postulaciones
GET /api/items?tiene_postulaciones=1      // Tambien acepta 1/0
```

---

## Operadores Disponibles

El metodo `applyDirectFilter` soporta estos operadores:

| Operador | Descripcion                          | Ejemplo URL                              |
|----------|--------------------------------------|------------------------------------------|
| `=`      | Igual (default)                      | `?estado=1`                              |
| `!=`     | Diferente                            | `?estado=0&estado_operator=!=`           |
| `>`      | Mayor que                            | `?puntaje=10&puntaje_operator=>`         |
| `<`      | Menor que                            | `?puntaje=20&puntaje_operator=<`         |
| `>=`     | Mayor o igual                        | `?puntaje=10&puntaje_operator=>=`        |
| `<=`     | Menor o igual                        | `?puntaje=20&puntaje_operator=<=`        |
| `LIKE`   | Contiene (agrega % automaticamente)  | `?nombre=juan&nombre_operator=like`      |
| `IN`     | En lista de valores                  | `?estado=1,2,3` (requiere operator: IN)  |

### Cambiar operador dinamicamente

Puedes cambiar el operador de cualquier filtro usando `{campo}_operator`:

```
GET /api/items?nombre=juan&nombre_operator=like
GET /api/items?puntaje=15&puntaje_operator=>
```

---

## Ordenamiento

### Configuracion

```php
protected function getSortConfig(): array
{
    return [
        'allowed' => ['id', 'nombre', 'created_at', 'facultad.nombre'],
        'default' => 'id',
    ];
}
```

### Parametros de URL

| Parametro    | Descripcion                    | Valores              |
|--------------|--------------------------------|----------------------|
| `sort_by`    | Columna para ordenar           | Cualquiera en allowed|
| `sort_order` | Direccion del ordenamiento     | `asc`, `desc`        |

### Ordenamiento por Relacion

Se puede ordenar por columnas de relaciones usando notacion de punto:

```php
'allowed' => ['id', 'nombre', 'facultad.nombre']
```

**URL de ejemplo:**
```
GET /api/items?sort_by=facultad.nombre&sort_order=asc
```

---

## Paginacion

### Parametros de URL

| Parametro  | Descripcion                          | Default |
|------------|--------------------------------------|---------|
| `per_page` | Cantidad de registros por pagina     | 15      |
| `page`     | Numero de pagina                     | 1       |
| `all`      | Retornar todos los registros (true)  | false   |

**URLs de ejemplo:**
```
GET /api/items?per_page=25
GET /api/items?per_page=50&page=2
GET /api/items?all=true
```

---

## Modelos de Ejemplo

A continuacion se presentan 3 modelos relacionados: **Facultad**, **ProgramaAcademico** y **Postulante**.

### Diagrama de Relaciones

```
+--------------+       +----------------------+       +--------------+
|   Facultad   |--1:N--|  ProgramaAcademico   |--1:N--|  Postulante  |
+--------------+       +----------------------+       +--------------+
      |                                                      |
      +------------------------1:N---------------------------+
```

### Modelo 1: Facultad

```php
<?php

namespace App\Models;

use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facultad extends Model
{
    use FlexibleQueries, SoftDeletes;

    protected $table = 'tb_facultades';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function programas(): HasMany
    {
        return $this->hasMany(ProgramaAcademico::class, 'facultad_id');
    }

    public function postulantes(): HasMany
    {
        return $this->hasMany(Postulante::class, 'facultad_id');
    }

    // ============================================
    // CONFIGURACION DE FLEXIBLEQUERIES
    // ============================================

    protected function getFilterConfig(): array
    {
        return [
            // Busqueda global en multiples columnas
            'search' => [
                'type' => 'global_search',
                'columns' => ['codigo', 'nombre', 'descripcion'],
            ],

            // Filtro por estado (operador = por defecto)
            'estado' => [
                'column' => 'estado',
            ],

            // Filtro por codigo con LIKE
            'codigo' => [
                'column' => 'codigo',
                'operator' => 'LIKE',
            ],

            // Filtro por nombre con LIKE
            'nombre' => [
                'column' => 'nombre',
                'operator' => 'LIKE',
            ],

            // Filtro de existencia: tiene programas?
            'tiene_programas' => [
                'type' => 'exists',
                'relation' => 'programas',
            ],

            // Filtro de existencia: tiene postulantes?
            'tiene_postulantes' => [
                'type' => 'exists',
                'relation' => 'postulantes',
            ],

            // Rango de fechas de creacion
            'created_at' => [
                'type' => 'date_range',
                'column' => 'created_at',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['id', 'codigo', 'nombre', 'estado', 'created_at', 'updated_at'],
            'default' => 'nombre',
        ];
    }
}
```

### Modelo 2: ProgramaAcademico

```php
<?php

namespace App\Models;

use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramaAcademico extends Model
{
    use FlexibleQueries, SoftDeletes;

    protected $table = 'tb_programas_academicos';

    protected $fillable = [
        'facultad_id',
        'codigo',
        'nombre',
        'modalidad',
        'duracion_semestres',
        'vacantes',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'duracion_semestres' => 'integer',
        'vacantes' => 'integer',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function facultad(): BelongsTo
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function postulantes(): HasMany
    {
        return $this->hasMany(Postulante::class, 'programa_id');
    }

    // ============================================
    // CONFIGURACION DE FLEXIBLEQUERIES
    // ============================================

    protected function getFilterConfig(): array
    {
        return [
            // Busqueda global incluyendo relaciones
            'search' => [
                'type' => 'global_search',
                'columns' => ['codigo', 'nombre', 'modalidad'],
                'relations' => [
                    'facultad' => ['nombre', 'codigo'],
                ],
            ],

            // Filtro por estado
            'estado' => [
                'column' => 'estado',
            ],

            // Filtro exacto por facultad_id
            'facultad_id' => [
                'column' => 'facultad_id',
            ],

            // Filtro multiple: varias facultades
            'facultades' => [
                'column' => 'facultad_id',
                'operator' => 'IN',
            ],

            // Filtro por nombre de facultad (relacion)
            'facultad_nombre' => [
                'type' => 'relation',
                'relation' => 'facultad',
                'column' => 'nombre',
                'operator' => 'LIKE',
            ],

            // Filtro por modalidad
            'modalidad' => [
                'column' => 'modalidad',
            ],

            // Rango de vacantes
            'vacantes' => [
                'type' => 'number_range',
                'column' => 'vacantes',
            ],

            // Rango de duracion
            'duracion' => [
                'type' => 'number_range',
                'column' => 'duracion_semestres',
            ],

            // Filtro de existencia: tiene postulantes?
            'tiene_postulantes' => [
                'type' => 'exists',
                'relation' => 'postulantes',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => [
                'id',
                'codigo',
                'nombre',
                'modalidad',
                'duracion_semestres',
                'vacantes',
                'estado',
                'created_at',
                'facultad.nombre',
            ],
            'default' => 'nombre',
        ];
    }
}
```

### Modelo 3: Postulante

```php
<?php

namespace App\Models;

use App\Http\Traits\FlexibleQueries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Postulante extends Model
{
    use FlexibleQueries, SoftDeletes;

    protected $table = 'tb_postulantes';

    protected $fillable = [
        'facultad_id',
        'programa_id',
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'telefono',
        'fecha_nacimiento',
        'puntaje',
        'estado_postulacion',
        'fecha_inscripcion',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_inscripcion' => 'datetime',
        'puntaje' => 'decimal:2',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function facultad(): BelongsTo
    {
        return $this->belongsTo(Facultad::class, 'facultad_id');
    }

    public function programa(): BelongsTo
    {
        return $this->belongsTo(ProgramaAcademico::class, 'programa_id');
    }

    // ============================================
    // CONFIGURACION DE FLEXIBLEQUERIES
    // ============================================

    protected function getFilterConfig(): array
    {
        return [
            // Busqueda global avanzada
            'search' => [
                'type' => 'global_search',
                'columns' => ['dni', 'nombres', 'apellido_paterno', 'apellido_materno', 'email'],
                'relations' => [
                    'facultad' => ['nombre'],
                    'programa' => ['nombre'],
                ],
            ],

            // Filtro exacto por DNI
            'dni' => [
                'column' => 'dni',
            ],

            // Filtro por email con LIKE
            'email' => [
                'column' => 'email',
                'operator' => 'LIKE',
            ],

            // Filtro exacto por facultad
            'facultad_id' => [
                'column' => 'facultad_id',
            ],

            // Filtro exacto por programa
            'programa_id' => [
                'column' => 'programa_id',
            ],

            // Filtro multiple: varios programas
            'programas' => [
                'column' => 'programa_id',
                'operator' => 'IN',
            ],

            // Filtro multiple: varias facultades
            'facultades' => [
                'column' => 'facultad_id',
                'operator' => 'IN',
            ],

            // Filtro por nombre de facultad (relacion)
            'facultad_nombre' => [
                'type' => 'relation',
                'relation' => 'facultad',
                'column' => 'nombre',
                'operator' => 'LIKE',
            ],

            // Filtro por nombre de programa (relacion)
            'programa_nombre' => [
                'type' => 'relation',
                'relation' => 'programa',
                'column' => 'nombre',
                'operator' => 'LIKE',
            ],

            // Filtro por estado de postulacion
            'estado_postulacion' => [
                'column' => 'estado_postulacion',
            ],

            // Filtro multiple por estados
            'estados' => [
                'column' => 'estado_postulacion',
                'operator' => 'IN',
            ],

            // Rango de puntaje
            'puntaje' => [
                'type' => 'number_range',
                'column' => 'puntaje',
            ],

            // Rango de fecha de inscripcion
            'fecha_inscripcion' => [
                'type' => 'date_range',
                'column' => 'fecha_inscripcion',
            ],

            // Rango de fecha de nacimiento
            'fecha_nacimiento' => [
                'type' => 'date_range',
                'column' => 'fecha_nacimiento',
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => [
                'id',
                'dni',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'email',
                'puntaje',
                'estado_postulacion',
                'fecha_inscripcion',
                'created_at',
                'facultad.nombre',
                'programa.nombre',
            ],
            'default' => 'created_at',
        ];
    }
}
```

---

## Uso en Controladores

### Controlador Basico

```php
<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use Illuminate\Http\Request;

class PostulanteController extends Controller
{
    public function index(Request $request)
    {
        $postulantes = Postulante::query()
            ->applyFilters($request)
            ->applySort($request)
            ->applyPagination($request);

        return response()->json($postulantes);
    }
}
```

### Controlador con Relaciones (Eager Loading)

```php
public function index(Request $request)
{
    $postulantes = Postulante::query()
        ->with(['facultad', 'programa'])
        ->applyFilters($request)
        ->applySort($request)
        ->applyPagination($request);

    return response()->json($postulantes);
}
```

### Controlador con Filtros Adicionales

```php
public function index(Request $request)
{
    $query = Postulante::query()
        ->with(['facultad', 'programa']);

    // Filtro adicional manual
    if (!$request->user()->isAdmin()) {
        $query->where('facultad_id', $request->user()->facultad_id);
    }

    $postulantes = $query
        ->applyFilters($request)
        ->applySort($request)
        ->applyPagination($request);

    return response()->json($postulantes);
}
```

---

## Ejemplos de URLs

### Facultad

```bash
# Busqueda global
GET /api/facultades?search=ingenieria

# Filtro por estado activo
GET /api/facultades?estado=1

# Filtro por codigo con LIKE
GET /api/facultades?codigo=FI

# Facultades que tienen programas
GET /api/facultades?tiene_programas=true

# Facultades sin postulantes
GET /api/facultades?tiene_postulantes=false

# Rango de fechas de creacion
GET /api/facultades?created_at_start=2026-01-01&created_at_end=2026-06-30

# Combinacion de filtros
GET /api/facultades?search=sistemas&estado=1&tiene_programas=true

# Con ordenamiento
GET /api/facultades?sort_by=nombre&sort_order=asc

# Con paginacion personalizada
GET /api/facultades?per_page=25&page=2

# Obtener todos sin paginar
GET /api/facultades?all=true

# Combinacion completa
GET /api/facultades?search=ingenieria&estado=1&sort_by=codigo&sort_order=desc&per_page=10
```

### ProgramaAcademico

```bash
# Busqueda global (incluye facultad)
GET /api/programas?search=software

# Filtro por facultad especifica
GET /api/programas?facultad_id=5

# Filtro por multiples facultades
GET /api/programas?facultades=1,2,3

# Filtro por nombre de facultad (relacion)
GET /api/programas?facultad_nombre=ingenieria

# Filtro por modalidad
GET /api/programas?modalidad=presencial

# Rango de vacantes
GET /api/programas?vacantes_min=20
GET /api/programas?vacantes_max=100
GET /api/programas?vacantes_min=20&vacantes_max=100

# Rango de duracion en semestres
GET /api/programas?duracion_min=8&duracion_max=12

# Programas con postulantes
GET /api/programas?tiene_postulantes=true

# Ordenar por nombre de facultad
GET /api/programas?sort_by=facultad.nombre&sort_order=asc

# Combinacion completa
GET /api/programas?search=sistemas&facultad_id=1&estado=1&vacantes_min=30&sort_by=nombre&sort_order=asc&per_page=15
```

### Postulante

```bash
# Busqueda global (incluye facultad y programa)
GET /api/postulantes?search=juan%20perez

# Busqueda por DNI exacto
GET /api/postulantes?dni=12345678

# Busqueda por email parcial
GET /api/postulantes?email=gmail.com

# Filtro por facultad
GET /api/postulantes?facultad_id=1

# Filtro por programa
GET /api/postulantes?programa_id=5

# Filtro por multiples programas
GET /api/postulantes?programas=1,2,3,4,5

# Filtro por nombre de facultad (relacion)
GET /api/postulantes?facultad_nombre=sistemas

# Filtro por nombre de programa (relacion)
GET /api/postulantes?programa_nombre=software

# Filtro por estado de postulacion
GET /api/postulantes?estado_postulacion=aprobado

# Filtro por multiples estados
GET /api/postulantes?estados=aprobado,pendiente,en_revision

# Rango de puntaje
GET /api/postulantes?puntaje_min=14
GET /api/postulantes?puntaje_max=20
GET /api/postulantes?puntaje_min=14&puntaje_max=18

# Rango de fecha de inscripcion
GET /api/postulantes?fecha_inscripcion_start=2026-01-01
GET /api/postulantes?fecha_inscripcion_end=2026-03-31
GET /api/postulantes?fecha_inscripcion_start=2026-01-01&fecha_inscripcion_end=2026-03-31

# Rango de fecha de nacimiento
GET /api/postulantes?fecha_nacimiento_start=2000-01-01&fecha_nacimiento_end=2005-12-31

# Ordenar por puntaje descendente
GET /api/postulantes?sort_by=puntaje&sort_order=desc

# Ordenar por nombre de facultad
GET /api/postulantes?sort_by=facultad.nombre&sort_order=asc

# Combinacion completa
GET /api/postulantes?search=juan&facultad_id=1&estado_postulacion=aprobado&puntaje_min=14&fecha_inscripcion_start=2026-01-01&sort_by=puntaje&sort_order=desc&per_page=20
```

---

## Combinaciones Avanzadas

### Busqueda con Multiples Criterios

```bash
# Postulantes aprobados con puntaje alto de la facultad de ingenieria
GET /api/postulantes?facultad_nombre=ingenieria&estado_postulacion=aprobado&puntaje_min=16&sort_by=puntaje&sort_order=desc

# Programas activos de multiples facultades con vacantes disponibles
GET /api/programas?facultades=1,2,3&estado=1&vacantes_min=10&sort_by=vacantes&sort_order=desc

# Facultades activas con programas, ordenadas alfabeticamente
GET /api/facultades?estado=1&tiene_programas=true&sort_by=nombre&sort_order=asc&all=true
```

---

## Resumen de Tipos de Filtros

| Tipo            | Descripcion                                    | Parametros requeridos            |
|-----------------|------------------------------------------------|----------------------------------|
| *(default)*     | Filtro directo con operador                    | `column`, `operator` (opcional)  |
| `global_search` | Busqueda LIKE en multiples columnas/relaciones | `columns`, `relations` (opcional)|
| `date_range`    | Rango de fechas con _start/_end                | `column`                         |
| `number_range`  | Rango numerico con _min/_max                   | `column`                         |
| `relation`      | Filtro en columna de relacion (whereHas)       | `relation`, `column`, `operator` |
| `exists`        | Existencia de relacion (true/false)            | `relation`                       |

---

## Resumen de Parametros URL

| Parametro              | Descripcion                                  | Ejemplo                           |
|------------------------|----------------------------------------------|-----------------------------------|
| `search`               | Busqueda global                              | `?search=juan`                    |
| `{campo}`              | Filtro directo                               | `?estado=1`                       |
| `{campo}_operator`     | Cambiar operador de filtro                   | `?nombre_operator=like`           |
| `{campo}_start`        | Inicio de rango de fecha                     | `?fecha_start=2026-01-01`         |
| `{campo}_end`          | Fin de rango de fecha                        | `?fecha_end=2026-12-31`           |
| `{campo}_min`          | Minimo de rango numerico                     | `?puntaje_min=14`                 |
| `{campo}_max`          | Maximo de rango numerico                     | `?puntaje_max=20`                 |
| `sort_by`              | Columna para ordenar                         | `?sort_by=nombre`                 |
| `sort_order`           | Direccion del orden (asc/desc)               | `?sort_order=desc`                |
| `per_page`             | Registros por pagina                         | `?per_page=25`                    |
| `page`                 | Numero de pagina                             | `?page=2`                         |
| `all`                  | Obtener todos (sin paginar)                  | `?all=true`                       |

---

## Notas Importantes

1. **Configuracion de `getSortConfig`**: Debe tener el formato con `allowed` y `default`:
   ```php
   protected function getSortConfig(): array
   {
       return [
           'allowed' => ['id', 'nombre', 'created_at'],
           'default' => 'id',
       ];
   }
   ```

2. **Filtros en relaciones**: Para que funcionen los filtros de tipo `relation`, la relacion debe estar definida en el modelo.

3. **Busqueda global en relaciones**: Usa `relations` en `global_search` para incluir columnas de modelos relacionados.

4. **Valores multiples (IN)**: Pueden enviarse como string separado por comas (`1,2,3`) o como array (`[]=1&[]=2`).

5. **Operadores permitidos**: `=`, `!=`, `>`, `<`, `>=`, `<=`, `LIKE`, `IN`.

6. **Ordenamiento por relacion**: Usa notacion de punto (`facultad.nombre`) y asegurate de incluirlo en `allowed`.
