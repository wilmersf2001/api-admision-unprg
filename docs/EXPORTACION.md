# Sistema de Exportación a Excel

Sistema declarativo para exportar modelos a Excel usando Laravel Excel.

## Instalación

El paquete ya está instalado en el proyecto:
```bash
composer require maatwebsite/excel
```

## Uso en el Modelo Postulant

El modelo ya está configurado con el trait `Exportable`. Puedes personalizar las columnas editando el método `getExportColumns()` en `app/Models/Postulant.php`.

### Configuración Actual

```php
protected function getExportColumns(): array
{
    return [
        // Columna simple
        'codigo' => 'Código',
        'nombres' => 'Nombres',

        // Relación (notación de punto)
        'gender.nombre' => 'Sexo',
        'districtBirth.nombre' => 'Distrito de Nacimiento',
        'academicProgram.nombre' => 'Programa Académico',

        // Con formato personalizado
        'fecha_nacimiento' => [
            'label' => 'Fecha de Nacimiento',
            'format' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : ''
        ],

        // Con callback (acceso completo al modelo)
        'tipo_documento' => [
            'label' => 'Tipo Documento',
            'format' => fn($value) => $value == self::DOCUMENT_TYPE_DNI ? 'DNI' : 'CE'
        ],
    ];
}
```

## API Endpoint

### Exportar Postulantes

**Endpoint:** `GET /api/postulants/export`

**Autenticación:** Requerida (Bearer Token)

**Parámetros de Query (opcionales):**

| Parámetro              | Tipo    | Descripción                           |
|------------------------|---------|---------------------------------------|
| modalidad_id           | integer | Filtrar por modalidad                 |
| programa_academico_id  | integer | Filtrar por programa académico        |
| estado_postulante_id   | integer | Filtrar por estado del postulante     |
| sede_id                | integer | Filtrar por sede                      |
| search                 | string  | Búsqueda por nombre, DNI o código     |
| ingreso                | boolean | Filtrar solo ingresantes (true/false) |

### Ejemplos de Uso

#### 1. Exportar todos los postulantes

```bash
curl -X GET "http://localhost/api/postulants/export" \
  -H "Authorization: Bearer {token}" \
  --output postulantes.xlsx
```

```javascript
// Frontend (JavaScript/Vue/React)
const exportAll = async () => {
  const response = await fetch('/api/postulants/export', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  const blob = await response.blob();
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'postulantes.xlsx';
  a.click();
};
```

#### 2. Exportar con filtros

```bash
curl -X GET "http://localhost/api/postulants/export?modalidad_id=1&sede_id=2" \
  -H "Authorization: Bearer {token}" \
  --output postulantes_filtrados.xlsx
```

```javascript
// Frontend con filtros
const exportFiltered = async (filters) => {
  const params = new URLSearchParams(filters);
  const response = await fetch(`/api/postulants/export?${params}`, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  const blob = await response.blob();
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `postulantes_${new Date().toISOString()}.xlsx`;
  a.click();
};

// Uso:
exportFiltered({
  modalidad_id: 1,
  programa_academico_id: 5,
  estado_postulante_id: 3
});
```

#### 3. Exportar solo ingresantes

```bash
curl -X GET "http://localhost/api/postulants/export?ingreso=true" \
  -H "Authorization: Bearer {token}" \
  --output ingresantes.xlsx
```

#### 4. Exportar con búsqueda

```bash
curl -X GET "http://localhost/api/postulants/export?search=Juan" \
  -H "Authorization: Bearer {token}" \
  --output busqueda.xlsx
```

## Agregar Exportación a Otros Modelos

Para agregar la funcionalidad de exportación a cualquier modelo:

### 1. Agregar el trait

```php
use App\Http\Traits\Exportable;

class TuModelo extends Model
{
    use Exportable;

    protected function getExportColumns(): array
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'relacion.campo' => 'Campo de Relación',
            'created_at' => [
                'label' => 'Fecha',
                'format' => fn($v) => $v->format('d/m/Y')
            ],
        ];
    }
}
```

### 2. Crear método en el controlador

```php
public function export(Request $request)
{
    try {
        $query = TuModelo::query();

        // Agregar filtros según necesites
        if ($request->has('filtro')) {
            $query->where('campo', $request->filtro);
        }

        $filename = 'archivo_' . now()->format('Y-m-d_His') . '.xlsx';

        return TuModelo::export($filename, $query);

    } catch (Exception $e) {
        return response()->json([
            'error' => 'Error al exportar: ' . $e->getMessage()
        ], 500);
    }
}
```

### 3. Agregar ruta

```php
Route::get('/tu-modelo/export', [TuModeloController::class, 'export']);
```

## Tipos de Configuración de Columnas

### Columna Simple
```php
'campo' => 'Nombre de la Columna'
```

### Relación
```php
'relacion.campo' => 'Nombre de la Columna'
'relacion.nestedRelacion.campo' => 'Relaciones Anidadas'
```

### Con Formato
```php
'campo' => [
    'label' => 'Nombre de la Columna',
    'format' => fn($value) => // tu lógica de formato
]
```

### Con Callback Completo (acceso al modelo)
```php
'campo' => [
    'label' => 'Nombre de la Columna',
    'value' => fn($model) => // acceso completo al modelo
]
```

## Características del Excel Generado

- Encabezados en negrita con fondo gris
- Columnas con auto-size
- Formato limpio y profesional
- Soporte para múltiples hojas (si lo configuras)

## Métodos Disponibles

### export(string $filename, $query = null)
Descarga directamente el archivo Excel

### exportAndStore(string $path, string $disk, $query = null)
Guarda el archivo en el storage del servidor

### exportToArray($query = null)
Retorna un array (útil para testing)

## Testing

```php
public function test_puede_exportar_postulantes()
{
    $this->actingAs($user);

    $response = $this->get('/api/postulants/export');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
}
```
