<?php

namespace App\Models;

use App\Http\Traits\Auditable;
use App\Http\Traits\Exportable;
use App\Http\Traits\FlexibleQueries;
use App\Http\Utils\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Postulant extends Model
{
    /** @use HasFactory<\Database\Factories\PostulantFactory> */
    use HasFactory, Auditable, FlexibleQueries, softDeletes, Exportable;

    protected $table = 'tb_postulante';

    protected $fillable = [
        'nombres',
        'ap_paterno',
        'ap_materno',
        'fecha_nacimiento',
        'num_documento',
        'tipo_documento',
        'num_documento_apoderado',
        'nombres_apoderado',
        'ap_paterno_apoderado',
        'ap_materno_apoderado',
        'num_voucher',
        'direccion',
        'correo',
        'telefono',
        'telefono_ap',
        'anno_egreso',
        'fecha_inscripcion',
        'num_veces_unprg',
        'num_veces_otros',
        'codigo',
        'ingreso',
        'sexo_id',
        'distrito_nac_id',
        'distrito_res_id',
        'tipo_direccion_id',
        'programa_academico_id',
        'colegio_id',
        'universidad_id',
        'modalidad_id',
        'sede_id',
        'pais_id',
        'estado_postulante_id',
    ];

    const DOCUMENT_TYPE_DNI = "1";
    const DOCUMENT_TYPE_CE = "2";

    protected function getFilterConfig(): array
    {
        return [
            'search' => [
                'type' => 'global_search',
                'columns' => ['nombres', 'ap_paterno', 'ap_materno', 'num_documento', 'correo', 'codigo'],
            ],
            'programa_academico_id' => [
                'columns' => ['programa_academico_id'],
            ],
            'modalidad_id' => [
                'columns' => ['modalidad_id'],
            ],
            'sede_id' => [
                'columns' => ['sede_id'],
            ],
            'estado_postulante_id' => [
                'columns' => ['estado_postulante_id'],
            ],
        ];
    }

    protected function getSortConfig(): array
    {
        return [
            'allowed' => ['nombres', 'ap_paterno', 'ap_materno', 'fecha_nacimiento', 'num_documento', 'correo', 'codigo', 'created_at', 'updated_at'],
            'default' => 'id'
        ];
    }

    public static function getImagePathByDni(Postulant $postulante)
    {
        $urlPhotoValid = Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_FOTO_CARNET . $postulante->num_documento . '.jpeg';

        if (Storage::disk(Constants::DISK_STORAGE)->exists($urlPhotoValid)) {
            $dniPath = Storage::url($urlPhotoValid);

            if (in_array($postulante->estado_postulante_id, Constants::ESTADOS_VALIDOS_POSTULANTE_ADMISION)) {
                return $dniPath;
            }
        }

        return $dniPath;
    }

    public static function bulkUpdateStatus($ids, $status)
    {
        return self::whereIn('id', $ids)->update(['estado_postulante_id' => $status]);
    }

    /**
     * Relación con el pago del banco
     */
    public function bank()
    {
        return $this->hasOne(Bank::class, 'postulant_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'sexo_id');
    }

    public function districtBirth()
    {
        return $this->belongsTo(District::class, 'distrito_nac_id');
    }

    public function districtResidence()
    {
        return $this->belongsTo(District::class, 'distrito_res_id');
    }

    public function addressType()
    {
        return $this->belongsTo(AddressType::class, 'tipo_direccion_id');
    }

    public function academicProgram()
    {
        return $this->belongsTo(AcademicProgram::class, 'programa_academico_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'colegio_id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'universidad_id');
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class, 'modalidad_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'pais_id');
    }

    public function postulantState()
    {
        return $this->belongsTo(PostulantState::class, 'estado_postulante_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'entity_id')
            ->where('entity_type', self::class);
    }

    /**
     * Configuración de columnas para exportación.
     */
    protected function getExportColumns(): array
    {
        return [
            'codigo' => 'Código',
            'nombres' => 'Nombres',
            'ap_paterno' => 'Apellido Paterno',
            'ap_materno' => 'Apellido Materno',
            'num_documento' => 'DNI',
            'tipo_documento' => [
                'label' => 'Tipo Documento',
                'format' => fn($value) => $value == self::DOCUMENT_TYPE_DNI ? 'DNI' : 'CE'
            ],
            'fecha_nacimiento' => [
                'label' => 'Fecha de Nacimiento',
                'format' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : ''
            ],
            'gender.nombre' => 'Sexo',
            'districtBirth.nombre' => 'Distrito de Nacimiento',
            'districtResidence.nombre' => 'Distrito de Residencia',
            'direccion' => 'Dirección',
            'addressType.nombre' => 'Tipo de Dirección',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'academicProgram.nombre' => 'Programa Académico',
            'modality.nombre' => 'Modalidad',
            'sede.nombre' => 'Sede',
            'school.nombre' => 'Colegio',
            'postulantState.nombre' => 'Estado',
            'fecha_inscripcion' => [
                'label' => 'Fecha de Inscripción',
                'format' => fn($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y H:i') : ''
            ],
            'created_at' => [
                'label' => 'Fecha de Registro',
                'format' => fn($value) => $value->format('d/m/Y H:i')
            ],
        ];
    }

    /**
     * Título de la hoja de exportación.
     */
    protected function getExportTitle(): string
    {
        return 'Postulantes';
    }

    /**
     * Query personalizado para exportación con relaciones optimizadas.
     */
    protected function getExportQuery()
    {
        return static::with([
            'gender',
            'districtBirth',
            'districtResidence',
            'addressType',
            'academicProgram',
            'modality',
            'sede',
            'school',
            'postulantState',
        ]);
    }
}
